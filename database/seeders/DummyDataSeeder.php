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
        $this->seedPlatformUsers();
        $this->seedPlatformInvoices();
        $this->seedPlatformTickets();
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
                'role' => 'platform_admin',
            ],
            [
                'name' => 'Support Staff',
                'email' => 'support@ispmanager.id',
                'password' => Hash::make('support123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
                'role' => 'platform_support',
            ],
            [
                'name' => 'Cashier Staff',
                'email' => 'cashier@ispmanager.id',
                'password' => Hash::make('cashier123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
                'role' => 'platform_cashier',
            ],
            [
                'name' => 'Technician Staff',
                'email' => 'technician@ispmanager.id',
                'password' => Hash::make('technician123'),
                'email_verified_at' => now(),
                'user_type' => 'platform',
                'is_active' => true,
                'role' => 'platform_technician',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Platform users seeded successfully.');
    }

    protected function seedPlatformInvoices(): void
    {
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->info('No tenants found, skipping platform invoices seeding.');
            return;
        }

        foreach ($tenants as $tenant) {
            $plan = SubscriptionPlan::where('slug', $tenant->subscription_plan)->first();
            $price = $plan ? $plan->price_monthly : 150000;

            for ($i = 0; $i < 3; $i++) {
                $issueDate = now()->subMonths($i);
                $dueDate = $issueDate->copy()->addDays(14);
                $status = $i === 0 ? 'pending' : ($i === 1 ? 'paid' : 'overdue');

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
        
        if ($tenants->isEmpty()) {
            $this->command->info('No tenants found, skipping platform tickets seeding.');
            return;
        }

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
