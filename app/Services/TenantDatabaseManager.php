<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantDatabaseManager
{
    protected static ?Tenant $currentTenant = null;
    protected static bool $initialized = false;
    protected static bool $connected = false;

    public static function setTenant(Tenant $tenant): void
    {
        self::$currentTenant = $tenant;
        self::$initialized = true;
        self::$connected = self::configureTenantConnection($tenant);
    }

    public static function getTenant(): ?Tenant
    {
        return self::$currentTenant;
    }

    public static function isInitialized(): bool
    {
        return self::$initialized;
    }

    public static function isConnected(): bool
    {
        return self::$connected;
    }

    public static function hasTenantDatabase(): bool
    {
        if (!self::$currentTenant) {
            return false;
        }
        
        $credentials = self::$currentTenant->getTenantDatabaseCredentials();
        return !empty($credentials['database']);
    }

    protected static function configureTenantConnection(Tenant $tenant): bool
    {
        $credentials = $tenant->getTenantDatabaseCredentials();

        if (empty($credentials['database'])) {
            Log::warning("Tenant {$tenant->subdomain} has no database configured.");
            return false;
        }

        try {
            Config::set('database.connections.tenant', [
                'driver' => 'mysql',
                'host' => $credentials['host'] ?? config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $credentials['database'],
                'username' => $credentials['username'] ?? config('database.connections.mysql.username'),
                'password' => $credentials['password'] ?? config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            DB::purge('tenant');
            DB::reconnect('tenant');
            
            DB::connection('tenant')->getPdo();
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to connect to tenant database for {$tenant->subdomain}: " . $e->getMessage());
            return false;
        }
    }

    public static function connectBySubdomain(string $subdomain): ?Tenant
    {
        $tenant = Tenant::where('subdomain', $subdomain)->first();
        
        if ($tenant) {
            self::setTenant($tenant);
        }
        
        return $tenant;
    }

    public static function disconnect(): void
    {
        DB::purge('tenant');
        self::$currentTenant = null;
        self::$initialized = false;
        self::$connected = false;
    }
}
