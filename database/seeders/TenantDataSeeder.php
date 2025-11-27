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
                'name' => 'Hotspot Harian 3 Jam',
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
                'name' => 'Hotspot Harian Full',
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
                'name' => 'Hotspot Mingguan',
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
        ];

        DB::connection('tenant')->table('service_plans')->insert($servicePlans);
        $this->command->info('Service plans created.');

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
                'name' => 'Router Cabang 1',
                'shortname' => 'ROUTER-CB1',
                'nasname' => '192.168.2.1',
                'ports' => 1812,
                'secret' => 'radiussecret456',
                'server' => null,
                'community' => 'public',
                'description' => 'Router cabang pertama',
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
                'vpn_username' => 'router-cb1',
                'vpn_password' => 'vpnpass123',
                'vpn_local_address' => '10.10.10.2',
                'vpn_remote_address' => '10.10.10.1',
                'last_seen' => now()->subMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotspot Cafe A',
                'shortname' => 'HS-CAFE-A',
                'nasname' => '192.168.10.1',
                'ports' => 1812,
                'secret' => 'hotspotsecret',
                'server' => null,
                'community' => 'public',
                'description' => 'Hotspot di Cafe A',
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
                'last_seen' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('nas')->insert($nasList);
        $this->command->info('NAS/Routers created.');

        $planIds = DB::connection('tenant')->table('service_plans')->pluck('id', 'name');

        $customers = [];
        $customerNames = [
            ['name' => 'Agus Setiawan', 'username' => 'agus.setiawan', 'email' => 'agus@email.com', 'phone' => '081234567001', 'address' => 'Jl. Merdeka No. 1'],
            ['name' => 'Budi Hartono', 'username' => 'budi.hartono', 'email' => 'budi@email.com', 'phone' => '081234567002', 'address' => 'Jl. Sudirman No. 25'],
            ['name' => 'Citra Dewi', 'username' => 'citra.dewi', 'email' => 'citra@email.com', 'phone' => '081234567003', 'address' => 'Jl. Gatot Subroto No. 10'],
            ['name' => 'Dedi Prasetyo', 'username' => 'dedi.prasetyo', 'email' => 'dedi@email.com', 'phone' => '081234567004', 'address' => 'Jl. Ahmad Yani No. 15'],
            ['name' => 'Eka Putri', 'username' => 'eka.putri', 'email' => 'eka@email.com', 'phone' => '081234567005', 'address' => 'Jl. Diponegoro No. 8'],
            ['name' => 'Fajar Hidayat', 'username' => 'fajar.hidayat', 'email' => 'fajar@email.com', 'phone' => '081234567006', 'address' => 'Jl. Teuku Umar No. 30'],
            ['name' => 'Gita Sari', 'username' => 'gita.sari', 'email' => 'gita@email.com', 'phone' => '081234567007', 'address' => 'Jl. Veteran No. 45'],
            ['name' => 'Hendra Wijaya', 'username' => 'hendra.wijaya', 'email' => 'hendra@email.com', 'phone' => '081234567008', 'address' => 'Jl. Imam Bonjol No. 12'],
            ['name' => 'Indah Permata', 'username' => 'indah.permata', 'email' => 'indah@email.com', 'phone' => '081234567009', 'address' => 'Jl. Hasanuddin No. 20'],
            ['name' => 'Joko Susanto', 'username' => 'joko.susanto', 'email' => 'joko@email.com', 'phone' => '081234567010', 'address' => 'Jl. Kartini No. 55'],
            ['name' => 'Kartika Sari', 'username' => 'kartika.sari', 'email' => 'kartika@email.com', 'phone' => '081234567011', 'address' => 'Jl. RA Kartini No. 7'],
            ['name' => 'Lukman Hakim', 'username' => 'lukman.hakim', 'email' => 'lukman@email.com', 'phone' => '081234567012', 'address' => 'Jl. Cut Nyak Dien No. 3'],
            ['name' => 'Maya Anggraini', 'username' => 'maya.anggraini', 'email' => 'maya@email.com', 'phone' => '081234567013', 'address' => 'Jl. Panglima Sudirman No. 18'],
            ['name' => 'Nanda Pratama', 'username' => 'nanda.pratama', 'email' => 'nanda@email.com', 'phone' => '081234567014', 'address' => 'Jl. Pahlawan No. 22'],
            ['name' => 'Oscar Ramadhan', 'username' => 'oscar.ramadhan', 'email' => 'oscar@email.com', 'phone' => '081234567015', 'address' => 'Jl. Pemuda No. 33'],
        ];

        $plans = ['Paket Hemat 5 Mbps', 'Paket Reguler 10 Mbps', 'Paket Premium 20 Mbps', 'Paket Ultra 50 Mbps'];
        $statuses = ['active', 'active', 'active', 'active', 'active', 'active', 'active', 'suspended', 'expired'];

        foreach ($customerNames as $index => $data) {
            $plan = $plans[array_rand($plans)];
            $status = $statuses[array_rand($statuses)];
            $registeredAt = now()->subDays(rand(30, 365));
            
            $customers[] = [
                'username' => $data['username'],
                'password' => Hash::make('password123'),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'service_plan_id' => $planIds[$plan] ?? 1,
                'service_type' => 'pppoe',
                'status' => $status,
                'registered_at' => $registeredAt,
                'expires_at' => $status === 'expired' ? now()->subDays(rand(1, 30)) : now()->addDays(rand(5, 25)),
                'suspended_at' => $status === 'suspended' ? now()->subDays(rand(1, 10)) : null,
                'suspend_reason' => $status === 'suspended' ? 'Belum bayar tagihan' : null,
                'balance' => 0,
                'auto_renew' => false,
                'created_at' => $registeredAt,
                'updated_at' => now(),
            ];
        }

        DB::connection('tenant')->table('customers')->insert($customers);
        $this->command->info('Customers created.');

        $hotspotPlans = ['Hotspot Harian 3 Jam', 'Hotspot Harian Full', 'Hotspot Mingguan'];
        $vouchers = [];
        $batchId = 'BATCH-' . strtoupper(Str::random(8));
        
        for ($i = 0; $i < 50; $i++) {
            $plan = $hotspotPlans[array_rand($hotspotPlans)];
            $statuses = ['unused', 'unused', 'unused', 'used', 'expired'];
            $status = $statuses[array_rand($statuses)];
            
            $vouchers[] = [
                'code' => strtoupper(Str::random(8)),
                'username' => 'v' . Str::random(6),
                'password' => Str::random(8),
                'service_plan_id' => $planIds[$plan] ?? 1,
                'status' => $status,
                'type' => 'single',
                'max_usage' => 1,
                'used_count' => $status === 'used' ? 1 : 0,
                'price' => $plan === 'Hotspot Harian 3 Jam' ? 5000 : ($plan === 'Hotspot Harian Full' ? 10000 : 35000),
                'batch_id' => $batchId,
                'generated_at' => now()->subDays(rand(1, 30)),
                'first_used_at' => $status === 'used' ? now()->subDays(rand(1, 7)) : null,
                'expires_at' => $status === 'expired' ? now()->subDays(rand(1, 5)) : null,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ];
        }

        DB::connection('tenant')->table('vouchers')->insert($vouchers);
        $this->command->info('Vouchers created.');

        $customerIds = DB::connection('tenant')->table('customers')->pluck('id')->toArray();
        $invoices = [];

        foreach ($customerIds as $customerId) {
            for ($month = 0; $month < 3; $month++) {
                $customer = DB::connection('tenant')->table('customers')
                    ->where('id', $customerId)
                    ->first();
                
                $plan = DB::connection('tenant')->table('service_plans')
                    ->where('id', $customer->service_plan_id)
                    ->first();
                
                $issueDate = now()->subMonths($month)->startOfMonth();
                $dueDate = $issueDate->copy()->addDays(7);
                $statuses = ['paid', 'paid', 'pending', 'overdue'];
                $status = $month === 0 ? $statuses[array_rand($statuses)] : 'paid';
                
                $invoiceNumber = 'INV-' . $issueDate->format('Ymd') . '-' . strtoupper(Str::random(6));
                $price = $plan->price ?? 100000;
                
                $invoices[] = [
                    'customer_id' => $customerId,
                    'service_plan_id' => $customer->service_plan_id,
                    'invoice_number' => $invoiceNumber,
                    'type' => 'subscription',
                    'subtotal' => $price,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => $price,
                    'status' => $status,
                    'issue_date' => $issueDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'paid_at' => $status === 'paid' ? $dueDate->copy()->subDays(rand(1, 5)) : null,
                    'payment_method' => $status === 'paid' ? ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])] : null,
                    'notes' => 'Tagihan internet bulan ' . $issueDate->format('F Y'),
                    'created_at' => $issueDate,
                    'updated_at' => now(),
                ];
            }
        }

        DB::connection('tenant')->table('invoices')->insert($invoices);
        $this->command->info('Invoices created.');

        $paidInvoices = DB::connection('tenant')->table('invoices')
            ->where('status', 'paid')
            ->get();

        $payments = [];
        foreach ($paidInvoices as $invoice) {
            $paymentMethods = ['cash', 'transfer', 'qris'];
            
            $payments[] = [
                'payment_id' => 'PAY-' . strtoupper(Str::random(12)),
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => $invoice->total,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_channel' => 'manual',
                'status' => 'success',
                'paid_at' => $invoice->paid_at,
                'created_at' => $invoice->paid_at,
                'updated_at' => now(),
            ];
        }

        if (!empty($payments)) {
            DB::connection('tenant')->table('payments')->insert($payments);
            $this->command->info('Payments created.');
        }

        $this->command->info('Tenant data seeding completed successfully!');
    }
}
