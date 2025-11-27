<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CpanelService
{
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $password;
    protected string $baseUrl;

    public function __construct()
    {
        $this->host = config('tenancy.cpanel.host');
        $this->port = config('tenancy.cpanel.port', 2083);
        $this->username = config('tenancy.cpanel.username');
        $this->password = config('tenancy.cpanel.password');
        $this->baseUrl = "https://{$this->host}:{$this->port}";
    }

    protected function makeRequest(string $module, string $function, array $params = []): array
    {
        $url = "{$this->baseUrl}/execute/{$module}/{$function}";
        
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] == 1) {
                    return [
                        'success' => true,
                        'data' => $data['data'] ?? null,
                        'messages' => $data['messages'] ?? [],
                    ];
                }
                
                return [
                    'success' => false,
                    'error' => $data['errors'][0] ?? 'Unknown error',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => "HTTP Error: " . $response->status(),
            ];
        } catch (Exception $e) {
            Log::error('cPanel API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createDatabase(string $dbName): array
    {
        $fullDbName = $this->username . '_' . $dbName;
        
        $result = $this->makeRequest('Mysql', 'create_database', [
            'name' => $fullDbName,
        ]);

        if ($result['success']) {
            Log::info("Database created: {$fullDbName}");
        }

        return array_merge($result, ['database_name' => $fullDbName]);
    }

    public function deleteDatabase(string $dbName): array
    {
        $fullDbName = str_starts_with($dbName, $this->username . '_') 
            ? $dbName 
            : $this->username . '_' . $dbName;

        return $this->makeRequest('Mysql', 'delete_database', [
            'name' => $fullDbName,
        ]);
    }

    public function createDatabaseUser(string $username, string $password): array
    {
        $fullUsername = $this->username . '_' . $username;

        $result = $this->makeRequest('Mysql', 'create_user', [
            'name' => $fullUsername,
            'password' => $password,
        ]);

        if ($result['success']) {
            Log::info("Database user created: {$fullUsername}");
        }

        return array_merge($result, ['username' => $fullUsername]);
    }

    public function deleteDatabaseUser(string $username): array
    {
        $fullUsername = str_starts_with($username, $this->username . '_') 
            ? $username 
            : $this->username . '_' . $username;

        return $this->makeRequest('Mysql', 'delete_user', [
            'name' => $fullUsername,
        ]);
    }

    public function setDatabaseUserPrivileges(string $username, string $database, array $privileges = ['ALL PRIVILEGES']): array
    {
        $fullUsername = str_starts_with($username, $this->username . '_') 
            ? $username 
            : $this->username . '_' . $username;
            
        $fullDbName = str_starts_with($database, $this->username . '_') 
            ? $database 
            : $this->username . '_' . $database;

        return $this->makeRequest('Mysql', 'set_privileges_on_database', [
            'user' => $fullUsername,
            'database' => $fullDbName,
            'privileges' => implode(',', $privileges),
        ]);
    }

    public function listDatabases(): array
    {
        return $this->makeRequest('Mysql', 'list_databases');
    }

    public function listDatabaseUsers(): array
    {
        return $this->makeRequest('Mysql', 'list_users');
    }

    public function provisionTenantDatabase(string $tenantId): array
    {
        $dbName = 't_' . substr(preg_replace('/[^a-z0-9]/', '', strtolower($tenantId)), 0, 10);
        $dbUser = $dbName;
        $dbPass = bin2hex(random_bytes(8));

        $existingDbs = $this->listDatabases();
        $fullDbName = $this->username . '_' . $dbName;
        $fullUsername = $this->username . '_' . $dbUser;
        
        $dbExists = false;
        $userExists = false;
        
        if ($existingDbs['success'] && isset($existingDbs['data'])) {
            foreach ($existingDbs['data'] as $db) {
                if ($db['database'] === $fullDbName) {
                    $dbExists = true;
                    if (!empty($db['users']) && in_array($fullUsername, $db['users'])) {
                        $userExists = true;
                    }
                    break;
                }
            }
        }

        if ($dbExists && $userExists) {
            Log::info("Database and user already exist: {$fullDbName}");
            return [
                'success' => true,
                'database' => $fullDbName,
                'username' => $fullUsername,
                'password' => $dbPass,
                'host' => env('DB_HOST', $this->host),
                'existing' => true,
            ];
        }

        if (!$dbExists) {
            $dbResult = $this->createDatabase($dbName);
            if (!$dbResult['success']) {
                if (strpos($dbResult['error'] ?? '', 'already exists') === false) {
                    return $dbResult;
                }
            }
        }

        if (!$userExists) {
            $userResult = $this->createDatabaseUser($dbUser, $dbPass);
            if (!$userResult['success']) {
                if (strpos($userResult['error'] ?? '', 'already exists') === false) {
                    return $userResult;
                }
            }
        }

        $privResult = $this->setDatabaseUserPrivileges($fullUsername, $fullDbName);
        if (!$privResult['success']) {
            Log::warning("Could not set privileges: " . ($privResult['error'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'database' => $fullDbName,
            'username' => $fullUsername,
            'password' => $dbPass,
            'host' => env('DB_HOST', $this->host),
        ];
    }

    public function deprovisionTenantDatabase(string $database, string $username): array
    {
        $userResult = $this->deleteDatabaseUser($username);
        $dbResult = $this->deleteDatabase($database);

        return [
            'success' => $userResult['success'] && $dbResult['success'],
            'user_deleted' => $userResult['success'],
            'database_deleted' => $dbResult['success'],
        ];
    }

    public function testConnection(): array
    {
        return $this->listDatabases();
    }
}
