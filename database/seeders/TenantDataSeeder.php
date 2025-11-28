<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBandwidthProfiles();
        $this->seedIpPools();
        $this->seedServicePlans();
        $this->seedNas();
        $this->seedPppoeProfiles();
        $this->seedHotspotProfiles();
        $this->seedPppoeServers();
        $this->seedHotspotServers();
        $this->seedCustomers();
        $this->seedVouchers();
        $this->seedInvoicesAndPayments();
        $this->seedRadiusData();
        $this->seedTenantSettings();
        $this->seedRolesAndPermissions();
        
        $this->command->info('Tenant data seeding completed successfully!');
    }

    protected function seedBandwidthProfiles(): void
    {
        $bandwidths = [
            [
                'name' => 'Bandwidth 1 Mbps',
                'name_bw' => 'bw-1m',
                'rate_up' => '512k',
                'rate_down' => '1M',
                'burst_limit_up' => '1M',
                'burst_limit_down' => '2M',
                'burst_threshold_up' => '384k',
                'burst_threshold_down' => '768k',
                'burst_time_up' => '10',
                'burst_time_down' => '10',
                'priority' => 8,
                'limit_at_up' => '256k',
                'limit_at_down' => '512k',
                'is_active' => true,
                'description' => 'Bandwidth 1 Mbps untuk paket hemat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bandwidth 5 Mbps',
                'name_bw' => 'bw-5m',
                'rate_up' => '2M',
                'rate_down' => '5M',
                'burst_limit_up' => '4M',
                'burst_limit_down' => '10M',
                'burst_threshold_up' => '1M',
                'burst_threshold_down' => '3M',
                'burst_time_up' => '10',
                'burst_time_down' => '10',
                'priority' => 7,
                'limit_at_up' => '1M',
                'limit_at_down' => '3M',
                'is_active' => true,
                'description' => 'Bandwidth 5 Mbps untuk paket reguler',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bandwidth 10 Mbps',
                'name_bw' => 'bw-10m',
                'rate_up' => '5M',
                'rate_down' => '10M',
                'burst_limit_up' => '8M',
                'burst_limit_down' => '15M',
                'burst_threshold_up' => '3M',
                'burst_threshold_down' => '7M',
                'burst_time_up' => '10',
                'burst_time_down' => '10',
                'priority' => 6,
                'limit_at_up' => '3M',
                'limit_at_down' => '5M',
                'is_active' => true,
                'description' => 'Bandwidth 10 Mbps untuk paket premium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bandwidth 20 Mbps',
                'name_bw' => 'bw-20m',
                'rate_up' => '10M',
                'rate_down' => '20M',
                'burst_limit_up' => '15M',
                'burst_limit_down' => '30M',
                'burst_threshold_up' => '7M',
                'burst_threshold_down' => '15M',
                'burst_time_up' => '10',
                'burst_time_down' => '10',
                'priority' => 5,
                'limit_at_up' => '5M',
                'limit_at_down' => '10M',
                'is_active' => true,
                'description' => 'Bandwidth 20 Mbps untuk paket bisnis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bandwidth 50 Mbps',
                'name_bw' => 'bw-50m',
                'rate_up' => '25M',
                'rate_down' => '50M',
                'burst_limit_up' => '40M',
                'burst_limit_down' => '80M',
                'burst_threshold_up' => '20M',
                'burst_threshold_down' => '40M',
                'burst_time_up' => '10',
                'burst_time_down' => '10',
                'priority' => 4,
                'limit_at_up' => '15M',
                'limit_at_down' => '30M',
                'is_active' => true,
                'description' => 'Bandwidth 50 Mbps untuk paket ultra',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Basic',
                'name_bw' => 'hs-basic',
                'rate_up' => '1M',
                'rate_down' => '2M',
                'burst_limit_up' => '2M',
                'burst_limit_down' => '4M',
                'burst_threshold_up' => '768k',
                'burst_threshold_down' => '1536k',
                'burst_time_up' => '8',
                'burst_time_down' => '8',
                'priority' => 8,
                'limit_at_up' => '512k',
                'limit_at_down' => '1M',
                'is_active' => true,
                'description' => 'Bandwidth hotspot basic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Premium',
                'name_bw' => 'hs-premium',
                'rate_up' => '3M',
                'rate_down' => '5M',
                'burst_limit_up' => '5M',
                'burst_limit_down' => '10M',
                'burst_threshold_up' => '2M',
                'burst_threshold_down' => '4M',
                'burst_time_up' => '8',
                'burst_time_down' => '8',
                'priority' => 6,
                'limit_at_up' => '1M',
                'limit_at_down' => '3M',
                'is_active' => true,
                'description' => 'Bandwidth hotspot premium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('bandwidth_profiles')->insert($bandwidths);
        $this->command->info('Bandwidth profiles created.');
    }

    protected function seedIpPools(): void
    {
        $pools = [
            [
                'name' => 'Pool PPPoE Utama',
                'pool_name' => 'pool-pppoe-main',
                'range_start' => '10.10.1.2',
                'range_end' => '10.10.1.254',
                'next_pool' => 'pool-pppoe-ext',
                'nas_id' => null,
                'type' => 'pppoe',
                'is_active' => true,
                'total_ips' => 253,
                'used_ips' => 45,
                'description' => 'Pool utama untuk pelanggan PPPoE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pool PPPoE Extended',
                'pool_name' => 'pool-pppoe-ext',
                'range_start' => '10.10.2.2',
                'range_end' => '10.10.2.254',
                'next_pool' => null,
                'nas_id' => null,
                'type' => 'pppoe',
                'is_active' => true,
                'total_ips' => 253,
                'used_ips' => 0,
                'description' => 'Pool extended untuk pelanggan PPPoE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pool Hotspot A',
                'pool_name' => 'pool-hotspot-a',
                'range_start' => '192.168.100.2',
                'range_end' => '192.168.100.254',
                'next_pool' => 'pool-hotspot-b',
                'nas_id' => null,
                'type' => 'hotspot',
                'is_active' => true,
                'total_ips' => 253,
                'used_ips' => 120,
                'description' => 'Pool untuk hotspot area A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pool Hotspot B',
                'pool_name' => 'pool-hotspot-b',
                'range_start' => '192.168.101.2',
                'range_end' => '192.168.101.254',
                'next_pool' => null,
                'nas_id' => null,
                'type' => 'hotspot',
                'is_active' => true,
                'total_ips' => 253,
                'used_ips' => 0,
                'description' => 'Pool untuk hotspot area B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pool VPN Client',
                'pool_name' => 'pool-vpn',
                'range_start' => '172.16.0.2',
                'range_end' => '172.16.0.254',
                'next_pool' => null,
                'nas_id' => null,
                'type' => 'both',
                'is_active' => true,
                'total_ips' => 253,
                'used_ips' => 5,
                'description' => 'Pool untuk VPN client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('ip_pools')->insert($pools);
        $this->command->info('IP Pools created.');
    }

    protected function seedServicePlans(): void
    {
        $servicePlans = [
            [
                'name' => 'Paket Hemat 5 Mbps',
                'code' => 'PKT-HEMAT-5',
                'description' => 'Paket internet hemat untuk browsing dan sosmed',
                'type' => 'pppoe',
                'price' => 100000,
                'validity' => 30,
                'validity_unit' => 'days',
                'bandwidth_down' => '5M',
                'bandwidth_up' => '2M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 1,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Reguler 10 Mbps',
                'code' => 'PKT-REGULER-10',
                'description' => 'Paket standar untuk rumah tangga',
                'type' => 'pppoe',
                'price' => 150000,
                'validity' => 30,
                'validity_unit' => 'days',
                'bandwidth_down' => '10M',
                'bandwidth_up' => '5M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 2,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Premium 20 Mbps',
                'code' => 'PKT-PREMIUM-20',
                'description' => 'Paket premium untuk streaming dan gaming',
                'type' => 'pppoe',
                'price' => 250000,
                'validity' => 30,
                'validity_unit' => 'days',
                'bandwidth_down' => '20M',
                'bandwidth_up' => '10M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => true,
                'max_devices' => 3,
                'simultaneous_use' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Ultra 50 Mbps',
                'code' => 'PKT-ULTRA-50',
                'description' => 'Paket ultra untuk kebutuhan profesional',
                'type' => 'pppoe',
                'price' => 450000,
                'validity' => 30,
                'validity_unit' => 'days',
                'bandwidth_down' => '50M',
                'bandwidth_up' => '25M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => true,
                'max_devices' => 5,
                'simultaneous_use' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot 1 Jam',
                'code' => 'HS-1JAM',
                'description' => 'Voucher hotspot 1 jam pemakaian',
                'type' => 'hotspot',
                'price' => 3000,
                'validity' => 1,
                'validity_unit' => 'hours',
                'bandwidth_down' => '3M',
                'bandwidth_up' => '1M',
                'quota_bytes' => 536870912,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 1,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot 3 Jam',
                'code' => 'HS-3JAM',
                'description' => 'Voucher hotspot 3 jam pemakaian',
                'type' => 'hotspot',
                'price' => 5000,
                'validity' => 3,
                'validity_unit' => 'hours',
                'bandwidth_down' => '5M',
                'bandwidth_up' => '2M',
                'quota_bytes' => 1073741824,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 1,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot 24 Jam',
                'code' => 'HS-24JAM',
                'description' => 'Voucher hotspot 24 jam unlimited',
                'type' => 'hotspot',
                'price' => 10000,
                'validity' => 24,
                'validity_unit' => 'hours',
                'bandwidth_down' => '10M',
                'bandwidth_up' => '5M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 1,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot 7 Hari',
                'code' => 'HS-7HARI',
                'description' => 'Voucher hotspot 7 hari',
                'type' => 'hotspot',
                'price' => 35000,
                'validity' => 7,
                'validity_unit' => 'days',
                'bandwidth_down' => '10M',
                'bandwidth_up' => '5M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 1,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot 30 Hari',
                'code' => 'HS-30HARI',
                'description' => 'Voucher hotspot bulanan',
                'type' => 'hotspot',
                'price' => 75000,
                'validity' => 30,
                'validity_unit' => 'days',
                'bandwidth_down' => '10M',
                'bandwidth_up' => '5M',
                'quota_bytes' => null,
                'has_fup' => false,
                'can_share' => false,
                'max_devices' => 2,
                'simultaneous_use' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('service_plans')->insert($servicePlans);
        $this->command->info('Service plans created.');
    }

    protected function seedNas(): void
    {
        $nasList = [
            [
                'name' => 'Router Pusat',
                'shortname' => 'ROUTER-PUSAT',
                'nasname' => '192.168.1.1',
                'ports' => 1812,
                'secret' => 'radiussecret123',
                'server' => null,
                'community' => 'public',
                'description' => 'Router utama di kantor pusat',
                'type' => 'mikrotik',
                'location_name' => 'Kantor Pusat Jakarta',
                'longitude' => 106.8456,
                'latitude' => -6.2088,
                'coverage' => 500,
                'api_username' => 'admin',
                'api_password' => 'password',
                'api_port' => 8728,
                'winbox_port' => 8291,
                'use_ssl' => false,
                'is_active' => true,
                'status' => 'enabled',
                'vpn_enabled' => false,
                'vpn_port' => 1701,
                'last_seen' => now()->subMinutes(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Router Cabang Bandung',
                'shortname' => 'ROUTER-BDG',
                'nasname' => '192.168.2.1',
                'ports' => 1812,
                'secret' => 'radiussecret456',
                'server' => null,
                'community' => 'public',
                'description' => 'Router cabang Bandung',
                'type' => 'mikrotik',
                'location_name' => 'Cabang Bandung',
                'longitude' => 107.6191,
                'latitude' => -6.9175,
                'coverage' => 300,
                'api_username' => 'admin',
                'api_password' => 'password',
                'api_port' => 8728,
                'winbox_port' => 8291,
                'use_ssl' => false,
                'is_active' => true,
                'status' => 'enabled',
                'vpn_enabled' => true,
                'vpn_secret' => 'vpnsecret123',
                'vpn_port' => 1701,
                'vpn_type' => 'l2tp',
                'vpn_server' => 'radius.example.com',
                'vpn_username' => 'router-bdg',
                'vpn_password' => 'vpnpass123',
                'vpn_local_address' => '10.10.10.2',
                'vpn_remote_address' => '10.10.10.1',
                'last_seen' => now()->subMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Cafe Aroma',
                'shortname' => 'HS-CAFE',
                'nasname' => '192.168.10.1',
                'ports' => 1812,
                'secret' => 'hotspotsecret',
                'server' => null,
                'community' => 'public',
                'description' => 'Hotspot di Cafe Aroma',
                'type' => 'mikrotik',
                'location_name' => 'Cafe Aroma Surabaya',
                'longitude' => 112.7508,
                'latitude' => -7.2575,
                'coverage' => 100,
                'api_username' => 'admin',
                'api_password' => 'password',
                'api_port' => 8728,
                'winbox_port' => 8291,
                'use_ssl' => false,
                'is_active' => true,
                'status' => 'enabled',
                'vpn_enabled' => false,
                'vpn_port' => 1701,
                'last_seen' => now()->subHours(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Mall Plaza',
                'shortname' => 'HS-MALL',
                'nasname' => '192.168.11.1',
                'ports' => 1812,
                'secret' => 'mallsecret',
                'server' => null,
                'community' => 'public',
                'description' => 'Hotspot di Mall Plaza',
                'type' => 'mikrotik',
                'location_name' => 'Mall Plaza Semarang',
                'longitude' => 110.4203,
                'latitude' => -6.9666,
                'coverage' => 200,
                'api_username' => 'admin',
                'api_password' => 'password',
                'api_port' => 8728,
                'winbox_port' => 8291,
                'use_ssl' => false,
                'is_active' => true,
                'status' => 'enabled',
                'vpn_enabled' => true,
                'vpn_secret' => 'mallvpn',
                'vpn_port' => 1701,
                'vpn_type' => 'l2tp',
                'vpn_server' => 'radius.example.com',
                'vpn_username' => 'router-mall',
                'vpn_password' => 'vpnpass456',
                'vpn_local_address' => '10.10.10.3',
                'vpn_remote_address' => '10.10.10.1',
                'last_seen' => now()->subMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('nas')->insert($nasList);
        $this->command->info('NAS/Routers created.');
    }

    protected function seedPppoeProfiles(): void
    {
        $nasIds = DB::connection('tenant')->table('nas')->pluck('id', 'shortname');
        $poolIds = DB::connection('tenant')->table('ip_pools')->pluck('id', 'pool_name');
        $bwIds = DB::connection('tenant')->table('bandwidth_profiles')->pluck('id', 'name_bw');

        $profiles = [
            [
                'name' => 'PPPoE Default',
                'profile_name' => 'pppoe-default',
                'nas_id' => $nasIds['ROUTER-PUSAT'] ?? null,
                'ip_pool_id' => $poolIds['pool-pppoe-main'] ?? null,
                'bandwidth_id' => $bwIds['bw-5m'] ?? null,
                'local_address' => '10.10.0.1',
                'remote_address' => 'pool-pppoe-main',
                'dns_server' => '8.8.8.8,8.8.4.4',
                'wins_server' => null,
                'session_timeout' => 0,
                'idle_timeout' => 0,
                'only_one' => true,
                'parent_queue' => 'none',
                'address_list' => 'pppoe-users',
                'is_active' => true,
                'description' => 'Profile default untuk pelanggan PPPoE',
                'mikrotik_options' => json_encode(['rate-limit' => '5M/10M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPPoE Premium',
                'profile_name' => 'pppoe-premium',
                'nas_id' => $nasIds['ROUTER-PUSAT'] ?? null,
                'ip_pool_id' => $poolIds['pool-pppoe-main'] ?? null,
                'bandwidth_id' => $bwIds['bw-20m'] ?? null,
                'local_address' => '10.10.0.1',
                'remote_address' => 'pool-pppoe-main',
                'dns_server' => '8.8.8.8,8.8.4.4',
                'wins_server' => null,
                'session_timeout' => 0,
                'idle_timeout' => 0,
                'only_one' => true,
                'parent_queue' => 'none',
                'address_list' => 'pppoe-premium',
                'is_active' => true,
                'description' => 'Profile untuk pelanggan PPPoE premium',
                'mikrotik_options' => json_encode(['rate-limit' => '10M/20M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPPoE Ultra',
                'profile_name' => 'pppoe-ultra',
                'nas_id' => $nasIds['ROUTER-PUSAT'] ?? null,
                'ip_pool_id' => $poolIds['pool-pppoe-main'] ?? null,
                'bandwidth_id' => $bwIds['bw-50m'] ?? null,
                'local_address' => '10.10.0.1',
                'remote_address' => 'pool-pppoe-main',
                'dns_server' => '8.8.8.8,1.1.1.1',
                'wins_server' => null,
                'session_timeout' => 0,
                'idle_timeout' => 0,
                'only_one' => false,
                'parent_queue' => 'none',
                'address_list' => 'pppoe-ultra',
                'is_active' => true,
                'description' => 'Profile untuk pelanggan PPPoE ultra',
                'mikrotik_options' => json_encode(['rate-limit' => '25M/50M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('pppoe_profiles')->insert($profiles);
        $this->command->info('PPPoE profiles created.');
    }

    protected function seedHotspotProfiles(): void
    {
        $nasIds = DB::connection('tenant')->table('nas')->pluck('id', 'shortname');
        $poolIds = DB::connection('tenant')->table('ip_pools')->pluck('id', 'pool_name');
        $bwIds = DB::connection('tenant')->table('bandwidth_profiles')->pluck('id', 'name_bw');

        $profiles = [
            [
                'name' => 'Hotspot Basic',
                'profile_name' => 'hs-basic',
                'nas_id' => $nasIds['HS-CAFE'] ?? null,
                'ip_pool_id' => $poolIds['pool-hotspot-a'] ?? null,
                'bandwidth_id' => $bwIds['hs-basic'] ?? null,
                'shared_users' => 1,
                'session_timeout' => 3600,
                'idle_timeout' => 300,
                'keepalive_timeout' => 2,
                'status_autorefresh' => '1m',
                'transparent_proxy' => false,
                'mac_cookie_timeout' => '3d',
                'parent_queue' => 'none',
                'address_list' => 'hotspot-users',
                'incoming_filter' => '',
                'outgoing_filter' => '',
                'is_active' => true,
                'description' => 'Profile hotspot basic untuk voucher harian',
                'mikrotik_options' => json_encode(['rate-limit' => '1M/2M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Standard',
                'profile_name' => 'hs-standard',
                'nas_id' => $nasIds['HS-CAFE'] ?? null,
                'ip_pool_id' => $poolIds['pool-hotspot-a'] ?? null,
                'bandwidth_id' => $bwIds['hs-basic'] ?? null,
                'shared_users' => 1,
                'session_timeout' => 86400,
                'idle_timeout' => 600,
                'keepalive_timeout' => 2,
                'status_autorefresh' => '1m',
                'transparent_proxy' => false,
                'mac_cookie_timeout' => '3d',
                'parent_queue' => 'none',
                'address_list' => 'hotspot-users',
                'incoming_filter' => '',
                'outgoing_filter' => '',
                'is_active' => true,
                'description' => 'Profile hotspot standard',
                'mikrotik_options' => json_encode(['rate-limit' => '2M/5M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Premium',
                'profile_name' => 'hs-premium',
                'nas_id' => $nasIds['HS-MALL'] ?? null,
                'ip_pool_id' => $poolIds['pool-hotspot-a'] ?? null,
                'bandwidth_id' => $bwIds['hs-premium'] ?? null,
                'shared_users' => 2,
                'session_timeout' => 0,
                'idle_timeout' => 900,
                'keepalive_timeout' => 2,
                'status_autorefresh' => '1m',
                'transparent_proxy' => false,
                'mac_cookie_timeout' => '7d',
                'parent_queue' => 'none',
                'address_list' => 'hotspot-premium',
                'incoming_filter' => '',
                'outgoing_filter' => '',
                'is_active' => true,
                'description' => 'Profile hotspot premium untuk voucher mingguan/bulanan',
                'mikrotik_options' => json_encode(['rate-limit' => '3M/5M']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('hotspot_profiles')->insert($profiles);
        $this->command->info('Hotspot profiles created.');
    }

    protected function seedPppoeServers(): void
    {
        $nasIds = DB::connection('tenant')->table('nas')->pluck('id', 'shortname');
        $profileIds = DB::connection('tenant')->table('pppoe_profiles')->pluck('id', 'profile_name');

        $servers = [
            [
                'name' => 'PPPoE Server Utama',
                'nas_id' => $nasIds['ROUTER-PUSAT'],
                'service_name' => 'pppoe-service',
                'interface' => 'ether2',
                'max_mtu' => 1480,
                'max_mru' => 1480,
                'max_sessions' => 0,
                'pppoe_profile_id' => $profileIds['pppoe-default'] ?? null,
                'authentication' => 'pap,chap,mschap1,mschap2',
                'keepalive' => true,
                'one_session_per_host' => true,
                'is_active' => true,
                'description' => 'Server PPPoE utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PPPoE Server Cabang',
                'nas_id' => $nasIds['ROUTER-BDG'],
                'service_name' => 'pppoe-cabang',
                'interface' => 'ether2',
                'max_mtu' => 1480,
                'max_mru' => 1480,
                'max_sessions' => 100,
                'pppoe_profile_id' => $profileIds['pppoe-default'] ?? null,
                'authentication' => 'pap,chap,mschap1,mschap2',
                'keepalive' => true,
                'one_session_per_host' => true,
                'is_active' => true,
                'description' => 'Server PPPoE cabang Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('pppoe_servers')->insert($servers);
        $this->command->info('PPPoE servers created.');
    }

    protected function seedHotspotServers(): void
    {
        $nasIds = DB::connection('tenant')->table('nas')->pluck('id', 'shortname');
        $profileIds = DB::connection('tenant')->table('hotspot_profiles')->pluck('id', 'profile_name');
        $poolIds = DB::connection('tenant')->table('ip_pools')->pluck('id', 'pool_name');

        $servers = [
            [
                'name' => 'Hotspot Cafe Aroma',
                'nas_id' => $nasIds['HS-CAFE'],
                'interface' => 'wlan1',
                'address_pool' => 'pool-hotspot-a',
                'ip_pool_id' => $poolIds['pool-hotspot-a'] ?? null,
                'hotspot_profile_id' => $profileIds['hs-basic'] ?? null,
                'login_by' => 'cookie,http-chap,http-pap',
                'http_cookie_lifetime' => '3d',
                'split_user_domain' => null,
                'https' => false,
                'is_active' => true,
                'description' => 'Server hotspot di Cafe Aroma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Mall Plaza',
                'nas_id' => $nasIds['HS-MALL'],
                'interface' => 'wlan1',
                'address_pool' => 'pool-hotspot-a',
                'ip_pool_id' => $poolIds['pool-hotspot-a'] ?? null,
                'hotspot_profile_id' => $profileIds['hs-premium'] ?? null,
                'login_by' => 'cookie,http-chap,http-pap,mac',
                'http_cookie_lifetime' => '7d',
                'split_user_domain' => null,
                'https' => true,
                'is_active' => true,
                'description' => 'Server hotspot di Mall Plaza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('hotspot_servers')->insert($servers);
        $this->command->info('Hotspot servers created.');
    }

    protected function seedCustomers(): void
    {
        $planIds = DB::connection('tenant')->table('service_plans')->pluck('id', 'code');
        $nasIds = DB::connection('tenant')->table('nas')->pluck('id', 'shortname');
        $pppoeProfileIds = DB::connection('tenant')->table('pppoe_profiles')->pluck('id', 'profile_name');

        $customerNames = [
            ['name' => 'Agus Setiawan', 'username' => 'agus.setiawan', 'email' => 'agus@email.com', 'phone' => '081234567001', 'address' => 'Jl. Merdeka No. 1, Jakarta'],
            ['name' => 'Budi Hartono', 'username' => 'budi.hartono', 'email' => 'budi@email.com', 'phone' => '081234567002', 'address' => 'Jl. Sudirman No. 25, Jakarta'],
            ['name' => 'Citra Dewi', 'username' => 'citra.dewi', 'email' => 'citra@email.com', 'phone' => '081234567003', 'address' => 'Jl. Gatot Subroto No. 10, Bandung'],
            ['name' => 'Dedi Prasetyo', 'username' => 'dedi.prasetyo', 'email' => 'dedi@email.com', 'phone' => '081234567004', 'address' => 'Jl. Ahmad Yani No. 15, Bandung'],
            ['name' => 'Eka Putri', 'username' => 'eka.putri', 'email' => 'eka@email.com', 'phone' => '081234567005', 'address' => 'Jl. Diponegoro No. 8, Surabaya'],
            ['name' => 'Fajar Hidayat', 'username' => 'fajar.hidayat', 'email' => 'fajar@email.com', 'phone' => '081234567006', 'address' => 'Jl. Teuku Umar No. 30, Surabaya'],
            ['name' => 'Gita Sari', 'username' => 'gita.sari', 'email' => 'gita@email.com', 'phone' => '081234567007', 'address' => 'Jl. Veteran No. 45, Semarang'],
            ['name' => 'Hendra Wijaya', 'username' => 'hendra.wijaya', 'email' => 'hendra@email.com', 'phone' => '081234567008', 'address' => 'Jl. Imam Bonjol No. 12, Semarang'],
            ['name' => 'Indah Permata', 'username' => 'indah.permata', 'email' => 'indah@email.com', 'phone' => '081234567009', 'address' => 'Jl. Hasanuddin No. 20, Yogyakarta'],
            ['name' => 'Joko Susanto', 'username' => 'joko.susanto', 'email' => 'joko@email.com', 'phone' => '081234567010', 'address' => 'Jl. Kartini No. 55, Yogyakarta'],
            ['name' => 'Kartika Sari', 'username' => 'kartika.sari', 'email' => 'kartika@email.com', 'phone' => '081234567011', 'address' => 'Jl. RA Kartini No. 7, Malang'],
            ['name' => 'Lukman Hakim', 'username' => 'lukman.hakim', 'email' => 'lukman@email.com', 'phone' => '081234567012', 'address' => 'Jl. Cut Nyak Dien No. 3, Malang'],
            ['name' => 'Maya Anggraini', 'username' => 'maya.anggraini', 'email' => 'maya@email.com', 'phone' => '081234567013', 'address' => 'Jl. Panglima Sudirman No. 18, Denpasar'],
            ['name' => 'Nanda Pratama', 'username' => 'nanda.pratama', 'email' => 'nanda@email.com', 'phone' => '081234567014', 'address' => 'Jl. Pahlawan No. 22, Denpasar'],
            ['name' => 'Oscar Ramadhan', 'username' => 'oscar.ramadhan', 'email' => 'oscar@email.com', 'phone' => '081234567015', 'address' => 'Jl. Pemuda No. 33, Medan'],
        ];

        $planCodes = ['PKT-HEMAT-5', 'PKT-REGULER-10', 'PKT-PREMIUM-20', 'PKT-ULTRA-50'];
        $statuses = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'suspended', 'expired'];
        $pppoeProfiles = ['pppoe-default', 'pppoe-premium', 'pppoe-ultra'];

        $customers = [];
        foreach ($customerNames as $index => $data) {
            $planCode = $planCodes[array_rand($planCodes)];
            $status = $statuses[array_rand($statuses)];
            $registeredAt = now()->subDays(rand(30, 365));
            $pppoeProfile = $pppoeProfiles[array_rand($pppoeProfiles)];
            
            $customers[] = [
                'username' => $data['username'],
                'password' => Hash::make('password123'),
                'pppoe_password' => 'pppoe' . rand(1000, 9999),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'service_plan_id' => $planIds[$planCode] ?? 1,
                'service_type' => 'pppoe',
                'nas_id' => $nasIds['ROUTER-PUSAT'] ?? null,
                'pppoe_profile_id' => $pppoeProfileIds[$pppoeProfile] ?? null,
                'mac_address' => sprintf('%02X:%02X:%02X:%02X:%02X:%02X', rand(0,255), rand(0,255), rand(0,255), rand(0,255), rand(0,255), rand(0,255)),
                'static_ip' => null,
                'status' => $status,
                'registered_at' => $registeredAt,
                'expires_at' => $status === 'expired' ? now()->subDays(rand(1, 30)) : now()->addDays(rand(5, 25)),
                'suspended_at' => $status === 'suspended' ? now()->subDays(rand(1, 10)) : null,
                'suspend_reason' => $status === 'suspended' ? 'Belum bayar tagihan' : null,
                'balance' => rand(0, 100000),
                'auto_renew' => rand(0, 1) ? true : false,
                'created_at' => $registeredAt,
                'updated_at' => now(),
            ];
        }

        DB::connection('tenant')->table('customers')->insert($customers);
        $this->command->info('Customers created (' . count($customers) . ' records).');
    }

    protected function seedVouchers(): void
    {
        $planIds = DB::connection('tenant')->table('service_plans')->pluck('id', 'code');
        
        $hotspotPlanCodes = ['HS-1JAM', 'HS-3JAM', 'HS-24JAM', 'HS-7HARI', 'HS-30HARI'];
        $prices = ['HS-1JAM' => 3000, 'HS-3JAM' => 5000, 'HS-24JAM' => 10000, 'HS-7HARI' => 35000, 'HS-30HARI' => 75000];
        
        $vouchers = [];
        $batchId1 = 'BATCH-' . now()->format('Ymd') . '-001';
        $batchId2 = 'BATCH-' . now()->format('Ymd') . '-002';
        
        for ($i = 0; $i < 100; $i++) {
            $planCode = $hotspotPlanCodes[array_rand($hotspotPlanCodes)];
            $statuses = ['unused', 'unused', 'unused', 'unused', 'used', 'expired'];
            $status = $statuses[array_rand($statuses)];
            $batchId = $i < 50 ? $batchId1 : $batchId2;
            
            $vouchers[] = [
                'code' => strtoupper(Str::random(8)),
                'username' => 'v' . strtolower(Str::random(6)),
                'password' => Str::random(8),
                'service_plan_id' => $planIds[$planCode] ?? 1,
                'status' => $status,
                'type' => 'single',
                'max_usage' => 1,
                'used_count' => $status === 'used' ? 1 : 0,
                'price' => $prices[$planCode],
                'batch_id' => $batchId,
                'generated_at' => now()->subDays(rand(1, 30)),
                'first_used_at' => $status === 'used' ? now()->subDays(rand(1, 7)) : null,
                'expires_at' => $status === 'expired' ? now()->subDays(rand(1, 5)) : null,
                'created_by' => null,
                'sold_by' => $status === 'used' ? null : null,
                'customer_id' => null,
                'notes' => null,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ];
        }

        DB::connection('tenant')->table('vouchers')->insert($vouchers);
        $this->command->info('Vouchers created (100 records in 2 batches).');
    }

    protected function seedInvoicesAndPayments(): void
    {
        $customers = DB::connection('tenant')->table('customers')->get();
        $invoices = [];
        $payments = [];

        foreach ($customers as $customer) {
            $plan = DB::connection('tenant')->table('service_plans')
                ->where('id', $customer->service_plan_id)
                ->first();
            
            for ($month = 0; $month < 3; $month++) {
                $issueDate = now()->subMonths($month)->startOfMonth();
                $dueDate = $issueDate->copy()->addDays(7);
                $statuses = ['paid', 'paid', 'pending', 'overdue'];
                $status = $month === 0 ? $statuses[array_rand($statuses)] : 'paid';
                
                $invoiceNumber = 'INV-' . $issueDate->format('Ymd') . '-' . strtoupper(Str::random(6));
                $price = $plan->price ?? 100000;
                $tax = round($price * 0.11);
                
                $invoiceId = DB::connection('tenant')->table('invoices')->insertGetId([
                    'customer_id' => $customer->id,
                    'service_plan_id' => $customer->service_plan_id,
                    'invoice_number' => $invoiceNumber,
                    'type' => 'subscription',
                    'subtotal' => $price,
                    'tax' => $tax,
                    'discount' => 0,
                    'total' => $price + $tax,
                    'status' => $status,
                    'issue_date' => $issueDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'paid_at' => $status === 'paid' ? $dueDate->copy()->subDays(rand(1, 5)) : null,
                    'payment_method' => $status === 'paid' ? ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])] : null,
                    'notes' => 'Tagihan internet bulan ' . $issueDate->translatedFormat('F Y'),
                    'created_at' => $issueDate,
                    'updated_at' => now(),
                ]);
                
                if ($status === 'paid') {
                    $payments[] = [
                        'payment_id' => 'PAY-' . strtoupper(Str::random(12)),
                        'invoice_id' => $invoiceId,
                        'customer_id' => $customer->id,
                        'amount' => $price + $tax,
                        'payment_method' => ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])],
                        'payment_channel' => 'manual',
                        'status' => 'success',
                        'paid_at' => $dueDate->copy()->subDays(rand(1, 5)),
                        'created_at' => $dueDate->copy()->subDays(rand(1, 5)),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($payments)) {
            DB::connection('tenant')->table('payments')->insert($payments);
        }
        
        $this->command->info('Invoices and payments created.');
    }

    protected function seedRadiusData(): void
    {
        $customers = DB::connection('tenant')->table('customers')
            ->where('status', 'active')
            ->get();

        $radcheckData = [];
        $radreplyData = [];
        $radusergroupData = [];

        foreach ($customers as $customer) {
            $plan = DB::connection('tenant')->table('service_plans')
                ->where('id', $customer->service_plan_id)
                ->first();

            $radcheckData[] = [
                'username' => $customer->username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $customer->pppoe_password ?? 'password123',
            ];

            if ($customer->expires_at) {
                $radcheckData[] = [
                    'username' => $customer->username,
                    'attribute' => 'Expiration',
                    'op' => ':=',
                    'value' => Carbon::parse($customer->expires_at)->format('d M Y H:i:s'),
                ];
            }

            if ($plan && $plan->simultaneous_use) {
                $radcheckData[] = [
                    'username' => $customer->username,
                    'attribute' => 'Simultaneous-Use',
                    'op' => ':=',
                    'value' => (string) $plan->simultaneous_use,
                ];
            }

            if ($plan) {
                $radreplyData[] = [
                    'username' => $customer->username,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M'),
                ];
            }

            $radusergroupData[] = [
                'username' => $customer->username,
                'groupname' => 'plan-' . $customer->service_plan_id,
                'priority' => 1,
            ];
        }

        if (!empty($radcheckData)) {
            DB::connection('tenant')->table('radcheck')->insert($radcheckData);
        }
        if (!empty($radreplyData)) {
            DB::connection('tenant')->table('radreply')->insert($radreplyData);
        }
        if (!empty($radusergroupData)) {
            DB::connection('tenant')->table('radusergroup')->insert($radusergroupData);
        }

        $plans = DB::connection('tenant')->table('service_plans')->get();
        $radgroupreplyData = [];
        
        foreach ($plans as $plan) {
            $radgroupreplyData[] = [
                'groupname' => 'plan-' . $plan->id,
                'attribute' => 'Mikrotik-Rate-Limit',
                'op' => ':=',
                'value' => ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M'),
            ];
        }

        if (!empty($radgroupreplyData)) {
            DB::connection('tenant')->table('radgroupreply')->insert($radgroupreplyData);
        }

        $this->command->info('RADIUS data created.');
    }

    protected function seedTenantSettings(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'ISP Demo', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_address', 'value' => 'Jl. Demo No. 123, Jakarta', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_phone', 'value' => '021-1234567', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_email', 'value' => 'info@ispdemo.com', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'IDR', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_rate', 'value' => '11', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_prefix', 'value' => 'INV', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_due_days', 'value' => '7', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'voucher_prefix', 'value' => '', 'group' => 'voucher', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'voucher_length', 'value' => '8', 'group' => 'voucher', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'voucher_uppercase', 'value' => 'true', 'group' => 'voucher', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'radius_server', 'value' => 'localhost', 'group' => 'radius', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'radius_port', 'value' => '1812', 'group' => 'radius', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'radius_secret', 'value' => 'testing123', 'group' => 'radius', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_suspend_days', 'value' => '3', 'group' => 'automation', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_disconnect_expired', 'value' => 'true', 'group' => 'automation', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::connection('tenant')->table('tenant_settings')->insert($settings);
        $this->command->info('Tenant settings created.');
    }

    protected function seedRolesAndPermissions(): void
    {
        $now = now();
        
        $permissions = [
            ['name' => 'customers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.suspend', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.reset', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.generate', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.print', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.pay', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.debug', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reports.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reports.financial', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reports.export', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'settings.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'settings.update', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'resellers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'resellers.manage', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'balance.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'balance.topup', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tickets.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tickets.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tickets.reply', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'radius.monitor', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'radius.disconnect', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'network.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'network.manage', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::connection('tenant')->table('permissions')->insert($permissions);

        $roles = [
            ['name' => 'owner', 'guard_name' => 'tenant', 'display_name' => 'Pemilik', 'description' => 'Akses penuh ke seluruh fitur tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'admin', 'guard_name' => 'tenant', 'display_name' => 'Administrator', 'description' => 'Mengelola operasional tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'technician', 'guard_name' => 'tenant', 'display_name' => 'Teknisi', 'description' => 'Akses teknis jaringan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cashier', 'guard_name' => 'tenant', 'display_name' => 'Kasir', 'description' => 'Kelola transaksi dan voucher', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support', 'guard_name' => 'tenant', 'display_name' => 'Dukungan', 'description' => 'Bantuan teknis ringan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reseller', 'guard_name' => 'tenant', 'display_name' => 'Reseller', 'description' => 'Kelola klien sendiri', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'investor', 'guard_name' => 'tenant', 'display_name' => 'Investor', 'description' => 'Akses laporan keuangan', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::connection('tenant')->table('roles')->insert($roles);

        $allPermissions = DB::connection('tenant')->table('permissions')->pluck('name')->toArray();
        
        $rolePermissions = [
            'owner' => $allPermissions,
            'admin' => array_diff($allPermissions, ['settings.update']),
            'technician' => ['customers.view', 'customers.reset', 'nas.view', 'nas.create', 'nas.edit', 'nas.debug', 'services.view', 'reports.view', 'radius.monitor', 'radius.disconnect', 'network.view', 'network.manage'],
            'cashier' => ['customers.view', 'customers.create', 'customers.edit', 'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay', 'reports.view', 'reports.financial', 'balance.view'],
            'support' => ['customers.view', 'customers.reset', 'vouchers.view', 'invoices.view', 'tickets.view', 'tickets.create', 'tickets.reply', 'radius.monitor'],
            'reseller' => ['customers.view', 'customers.create', 'customers.edit', 'vouchers.view', 'vouchers.generate', 'vouchers.print', 'invoices.view', 'invoices.create', 'reports.view', 'balance.view', 'balance.topup'],
            'investor' => ['reports.view', 'reports.financial'],
        ];

        $permissionIds = DB::connection('tenant')->table('permissions')->pluck('id', 'name');
        $roleIds = DB::connection('tenant')->table('roles')->pluck('id', 'name');

        $roleHasPermissions = [];
        foreach ($rolePermissions as $roleName => $perms) {
            $roleId = $roleIds[$roleName];
            foreach ($perms as $permName) {
                if (isset($permissionIds[$permName])) {
                    $roleHasPermissions[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionIds[$permName],
                    ];
                }
            }
        }

        DB::connection('tenant')->table('role_has_permissions')->insert($roleHasPermissions);
        $this->command->info('Tenant roles and permissions created.');
    }
}
