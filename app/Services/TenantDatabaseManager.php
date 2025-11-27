<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantDatabaseManager
{
    protected static ?Tenant $currentTenant = null;
    protected static bool $initialized = false;

    public static function setTenant(Tenant $tenant): void
    {
        self::$currentTenant = $tenant;
        self::configureTenantConnection($tenant);
        self::$initialized = true;
    }

    public static function getTenant(): ?Tenant
    {
        return self::$currentTenant;
    }

    public static function isInitialized(): bool
    {
        return self::$initialized;
    }

    protected static function configureTenantConnection(Tenant $tenant): void
    {
        $credentials = $tenant->getTenantDatabaseCredentials();

        if (!$credentials['database']) {
            throw new \Exception("Tenant {$tenant->subdomain} has no database configured.");
        }

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
    }
}
