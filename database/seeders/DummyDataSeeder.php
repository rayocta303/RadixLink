<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\PlatformInvoice;
use App\Models\PlatformTicket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSubscriptionPlans();
        $this->seedPlatformUsers();
        $this->seedPlatformInvoices();
        $this->seedPlatformTickets();
    }

    protected function seedSubscriptionPlans(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Untuk ISP kecil dengan kebutuhan dasar',
                'price_monthly' => 150000,
                'price_yearly' => 1500000,
                'max_routers' => 3,
                'max_users' => 500,
                'max_vouchers' => 5000,
                'features' => json_encode([
                    'basic_reports' => true,
                    'email_support' => true,
                    'api_access' => false,
                    'custom_branding' => false,
                    'priority_support' => false,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'Untuk ISP menengah dengan fitur lengkap',
                'price_monthly' => 350000,
                'price_yearly' => 3500000,
                'max_routers' => 10,
                'max_users' => 2000,
                'max_vouchers' => 20000,
                'features' => json_encode([
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'email_support' => true,
                    'phone_support' => true,
                    'api_access' => true,
                    'custom_branding' => false,
                    'priority_support' => false,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Untuk ISP besar dengan semua fitur',
                'price_monthly' => 750000,
                'price_yearly' => 7500000,
                'max_routers' => 50,
                'max_users' => 10000,
                'max_vouchers' => 100000,
                'features' => json_encode([
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'email_support' => true,
                    'phone_support' => true,
                    'api_access' => true,
                    'custom_branding' => true,
                    'priority_support' => true,
                    'dedicated_manager' => true,
                    'sla_guarantee' => true,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Solusi kustom untuk perusahaan besar',
                'price_monthly' => 1500000,
                'price_yearly' => 15000000,
                'max_routers' => 999,
                'max_users' => 999999,
                'max_vouchers' => 999999,
                'features' => json_encode([
                    'unlimited' => true,
                    'all_features' => true,
                    'priority_support' => true,
                    'dedicated_manager' => true,
                    'custom_development' => true,
                    'on_premise_option' => true,
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully.');
    }

    protected function seedPlatformUsers(): void
    {
        $users = [
            [
                'name' => 'Platform Admin',
                'email' => 'platform@ispmanager.id',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
            ],
            [
                'name' => 'Support Staff',
                'email' => 'support@ispmanager.id',
                'password' => Hash::make('support123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing Team',
                'email' => 'marketing@ispmanager.id',
                'password' => Hash::make('marketing123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if ($userData['email'] === 'platform@ispmanager.id') {
                $user->assignRole('platform_admin');
            } elseif ($userData['email'] === 'support@ispmanager.id') {
                $user->assignRole('platform_support');
            }
        }

        $this->command->info('Platform users seeded successfully.');
    }

    protected function seedPlatformInvoices(): void
    {
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            for ($i = 0; $i < 3; $i++) {
                $issueDate = now()->subMonths($i);
                $dueDate = $issueDate->copy()->addDays(14);
                $status = $i === 0 ? 'pending' : ($i === 1 ? 'paid' : 'overdue');
                
                $price = match($tenant->subscription_plan) {
                    'premium' => 750000,
                    'standard' => 350000,
                    default => 150000,
                };

                PlatformInvoice::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'invoice_number' => 'INV-' . strtoupper($tenant->subdomain) . '-' . $issueDate->format('Ym'),
                    ],
                    [
                        'subtotal' => $price,
                        'tax' => $price * 0.11,
                        'discount' => 0,
                        'total' => $price * 1.11,
                        'status' => $status,
                        'issue_date' => $issueDate,
                        'due_date' => $dueDate,
                        'paid_at' => $status === 'paid' ? $dueDate->copy()->subDays(3) : null,
                        'payment_method' => $status === 'paid' ? 'bank_transfer' : null,
                        'notes' => 'Invoice langganan bulanan ' . $issueDate->format('F Y'),
                    ]
                );
            }
        }

        $this->command->info('Platform invoices seeded successfully.');
    }

    protected function seedPlatformTickets(): void
    {
        $tenants = Tenant::all();
        
        $ticketSubjects = [
            ['Tidak bisa login ke dashboard', 'urgent', 'open'],
            ['Request penambahan kuota router', 'medium', 'in_progress'],
            ['Integrasi dengan MikroTik gagal', 'high', 'open'],
            ['Pertanyaan tentang fitur voucher', 'low', 'resolved'],
            ['Upgrade paket langganan', 'medium', 'closed'],
            ['Error saat generate laporan', 'high', 'in_progress'],
            ['Request custom branding', 'low', 'open'],
            ['Masalah pembayaran invoice', 'urgent', 'open'],
        ];

        foreach ($tenants as $tenant) {
            $numTickets = rand(2, 4);
            $selectedTickets = array_rand($ticketSubjects, $numTickets);
            
            foreach ((array)$selectedTickets as $index) {
                $ticket = $ticketSubjects[$index];
                $createdAt = now()->subDays(rand(1, 30));
                
                PlatformTicket::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'subject' => $ticket[0],
                    ],
                    [
                        'ticket_number' => 'TKT-' . strtoupper(Str::random(8)),
                        'message' => 'Halo, saya mengalami masalah terkait: ' . $ticket[0] . '. Mohon bantuannya untuk menyelesaikan hal ini. Terima kasih.',
                        'priority' => $ticket[1],
                        'status' => $ticket[2],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
            }
        }

        $this->command->info('Platform tickets seeded successfully.');
    }
}
