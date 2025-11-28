<?php

namespace App\Services;

use App\Models\Tenant\Customer;
use App\Models\Tenant\ServicePlan;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Nas;
use App\Models\Tenant\Radcheck;
use App\Models\Tenant\Radreply;
use App\Models\Tenant\Radusergroup;
use App\Models\Tenant\Radgroupcheck;
use App\Models\Tenant\Radgroupreply;
use App\Models\Tenant\Radacct;
use App\Models\Tenant\Radpostauth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RadiusService
{
    public function syncCustomer(Customer $customer): bool
    {
        if (!$customer->servicePlan) {
            Log::warning("RadiusService: Customer {$customer->username} has no service plan");
            return false;
        }

        try {
            DB::connection('tenant')->beginTransaction();

            $plan = $customer->servicePlan;
            $username = $customer->username;
            $password = $customer->pppoe_password ?? $customer->password ?? 'password123';

            Radcheck::where('username', $username)->delete();
            Radreply::where('username', $username)->delete();
            Radusergroup::where('username', $username)->delete();

            Radcheck::create([
                'username' => $username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $password,
            ]);

            if ($customer->expires_at) {
                $expiration = Carbon::parse($customer->expires_at)->format('d M Y H:i:s');
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Expiration',
                    'op' => ':=',
                    'value' => $expiration,
                ]);
            }

            if ($plan->simultaneous_use && $plan->simultaneous_use > 0) {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Simultaneous-Use',
                    'op' => ':=',
                    'value' => (string) $plan->simultaneous_use,
                ]);
            }

            if ($customer->status === 'suspended') {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Auth-Type',
                    'op' => ':=',
                    'value' => 'Reject',
                ]);
            }

            if ($plan->bandwidth_down || $plan->bandwidth_up) {
                $rateLimit = ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M');
                
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => $rateLimit,
                ]);
            }

            if ($customer->static_ip) {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Framed-IP-Address',
                    'op' => ':=',
                    'value' => $customer->static_ip,
                ]);
            }

            if ($customer->nas && $customer->nas->nasname) {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'NAS-IP-Address',
                    'op' => ':=',
                    'value' => $customer->nas->nasname,
                ]);
            }

            if ($customer->pppoeProfile && $customer->pppoeProfile->address_list) {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Mikrotik-Address-List',
                    'op' => '+=',
                    'value' => $customer->pppoeProfile->address_list,
                ]);
            }

            if ($plan->session_timeout && $plan->session_timeout > 0) {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Session-Timeout',
                    'op' => ':=',
                    'value' => (string) $plan->session_timeout,
                ]);
            }

            if ($plan->idle_timeout && $plan->idle_timeout > 0) {
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Idle-Timeout',
                    'op' => ':=',
                    'value' => (string) $plan->idle_timeout,
                ]);
            }

            $groupName = 'plan-' . $plan->id;
            Radusergroup::create([
                'username' => $username,
                'groupname' => $groupName,
                'priority' => 1,
            ]);

            $this->syncServicePlan($plan);

            DB::connection('tenant')->commit();

            Log::info("RadiusService: Successfully synced customer {$username}");
            return true;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            Log::error("RadiusService: Failed to sync customer {$customer->username}: " . $e->getMessage());
            return false;
        }
    }

    public function syncVoucher(Voucher $voucher): bool
    {
        if (!$voucher->servicePlan) {
            Log::warning("RadiusService: Voucher {$voucher->code} has no service plan");
            return false;
        }

        try {
            DB::connection('tenant')->beginTransaction();

            $plan = $voucher->servicePlan;
            $username = $voucher->username ?? $voucher->code;
            $password = $voucher->password ?? $voucher->code;

            Radcheck::where('username', $username)->delete();
            Radreply::where('username', $username)->delete();
            Radusergroup::where('username', $username)->delete();

            Radcheck::create([
                'username' => $username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $password,
            ]);

            if ($voucher->expires_at) {
                $expiration = Carbon::parse($voucher->expires_at)->format('d M Y H:i:s');
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Expiration',
                    'op' => ':=',
                    'value' => $expiration,
                ]);
            }

            if ($voucher->status !== 'unused') {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Auth-Type',
                    'op' => ':=',
                    'value' => 'Reject',
                ]);
            }

            if ($plan->simultaneous_use && $plan->simultaneous_use > 0) {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Simultaneous-Use',
                    'op' => ':=',
                    'value' => (string) $plan->simultaneous_use,
                ]);
            }

            if ($plan->max_usage && $plan->max_usage > 0) {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Max-All-Session',
                    'op' => ':=',
                    'value' => (string) $plan->max_usage,
                ]);
            }

            if ($plan->bandwidth_down || $plan->bandwidth_up) {
                $rateLimit = ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M');
                
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => $rateLimit,
                ]);
            }

            if ($plan->validity && $plan->validity_unit) {
                $sessionTimeout = $this->calculateSessionTimeout($plan->validity, $plan->validity_unit);
                
                Radreply::create([
                    'username' => $username,
                    'attribute' => 'Session-Timeout',
                    'op' => ':=',
                    'value' => (string) $sessionTimeout,
                ]);
            }

            if ($plan->quota_bytes && $plan->quota_bytes > 0) {
                Radcheck::create([
                    'username' => $username,
                    'attribute' => 'Max-Data',
                    'op' => ':=',
                    'value' => (string) $plan->quota_bytes,
                ]);
            }

            $groupName = 'plan-' . $plan->id;
            Radusergroup::create([
                'username' => $username,
                'groupname' => $groupName,
                'priority' => 1,
            ]);

            $this->syncServicePlan($plan);

            DB::connection('tenant')->commit();

            Log::info("RadiusService: Successfully synced voucher {$voucher->code}");
            return true;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            Log::error("RadiusService: Failed to sync voucher {$voucher->code}: " . $e->getMessage());
            return false;
        }
    }

    public function syncServicePlan(ServicePlan $plan): bool
    {
        try {
            $groupName = 'plan-' . $plan->id;

            Radgroupreply::where('groupname', $groupName)->delete();
            Radgroupcheck::where('groupname', $groupName)->delete();

            if ($plan->bandwidth_down || $plan->bandwidth_up) {
                $rateLimit = ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M');
                
                Radgroupreply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => $rateLimit,
                ]);
            }

            if ($plan->validity && $plan->validity_unit) {
                $sessionTimeout = $this->calculateSessionTimeout($plan->validity, $plan->validity_unit);
                
                Radgroupreply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Session-Timeout',
                    'op' => ':=',
                    'value' => (string) $sessionTimeout,
                ]);
            }

            if ($plan->idle_timeout && $plan->idle_timeout > 0) {
                Radgroupreply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Idle-Timeout',
                    'op' => ':=',
                    'value' => (string) $plan->idle_timeout,
                ]);
            }

            if ($plan->simultaneous_use && $plan->simultaneous_use > 0) {
                Radgroupcheck::create([
                    'groupname' => $groupName,
                    'attribute' => 'Simultaneous-Use',
                    'op' => ':=',
                    'value' => (string) $plan->simultaneous_use,
                ]);
            }

            Log::info("RadiusService: Successfully synced service plan {$plan->name}");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to sync service plan {$plan->name}: " . $e->getMessage());
            return false;
        }
    }

    public function syncNas(Nas $nas): bool
    {
        try {
            DB::connection('tenant')->table('nas')
                ->where('id', $nas->id)
                ->update([
                    'ports' => $nas->ports ?? 1812,
                    'secret' => $nas->secret,
                    'type' => $nas->type ?? 'other',
                    'community' => $nas->community,
                    'description' => $nas->description,
                ]);

            Log::info("RadiusService: Successfully synced NAS {$nas->shortname}");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to sync NAS {$nas->shortname}: " . $e->getMessage());
            return false;
        }
    }

    public function removeCustomer(Customer $customer): bool
    {
        try {
            $username = $customer->username;
            
            Radcheck::where('username', $username)->delete();
            Radreply::where('username', $username)->delete();
            Radusergroup::where('username', $username)->delete();

            Log::info("RadiusService: Removed customer {$username} from RADIUS");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to remove customer {$customer->username}: " . $e->getMessage());
            return false;
        }
    }

    public function removeVoucher(Voucher $voucher): bool
    {
        try {
            $username = $voucher->username ?? $voucher->code;
            
            Radcheck::where('username', $username)->delete();
            Radreply::where('username', $username)->delete();
            Radusergroup::where('username', $username)->delete();

            Log::info("RadiusService: Removed voucher {$voucher->code} from RADIUS");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to remove voucher {$voucher->code}: " . $e->getMessage());
            return false;
        }
    }

    public function suspendCustomer(Customer $customer): bool
    {
        try {
            $username = $customer->username;
            
            Radcheck::where('username', $username)
                ->where('attribute', 'Auth-Type')
                ->delete();
            
            Radcheck::create([
                'username' => $username,
                'attribute' => 'Auth-Type',
                'op' => ':=',
                'value' => 'Reject',
            ]);

            $this->disconnectUser($username);

            Log::info("RadiusService: Suspended customer {$username}");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to suspend customer {$customer->username}: " . $e->getMessage());
            return false;
        }
    }

    public function activateCustomer(Customer $customer): bool
    {
        try {
            $username = $customer->username;
            
            Radcheck::where('username', $username)
                ->where('attribute', 'Auth-Type')
                ->where('value', 'Reject')
                ->delete();

            Log::info("RadiusService: Activated customer {$username}");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to activate customer {$customer->username}: " . $e->getMessage());
            return false;
        }
    }

    public function getActiveSessionsForUser(string $username): \Illuminate\Database\Eloquent\Collection
    {
        return Radacct::where('username', $username)
            ->whereNull('acctstoptime')
            ->orderBy('acctstarttime', 'desc')
            ->get();
    }

    public function getActiveSessionsForNas(string $nasIpAddress): \Illuminate\Database\Eloquent\Collection
    {
        return Radacct::where('nasipaddress', $nasIpAddress)
            ->whereNull('acctstoptime')
            ->orderBy('acctstarttime', 'desc')
            ->get();
    }

    public function getAllActiveSessions(): \Illuminate\Database\Eloquent\Collection
    {
        return Radacct::whereNull('acctstoptime')
            ->orderBy('acctstarttime', 'desc')
            ->get();
    }

    public function getActiveSessionCount(): int
    {
        return Radacct::whereNull('acctstoptime')->count();
    }

    public function getUsageStats(string $username, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = Radacct::where('username', $username);

        if ($from) {
            $query->where('acctstarttime', '>=', $from);
        }
        if ($to) {
            $query->where('acctstarttime', '<=', $to);
        }

        $records = $query->get();

        return [
            'total_sessions' => $records->count(),
            'total_time' => $records->sum('acctsessiontime'),
            'total_time_formatted' => $this->formatSeconds($records->sum('acctsessiontime')),
            'total_download' => $records->sum('acctoutputoctets'),
            'total_download_formatted' => $this->formatBytes($records->sum('acctoutputoctets')),
            'total_upload' => $records->sum('acctinputoctets'),
            'total_upload_formatted' => $this->formatBytes($records->sum('acctinputoctets')),
            'total_traffic' => $records->sum('acctoutputoctets') + $records->sum('acctinputoctets'),
            'total_traffic_formatted' => $this->formatBytes($records->sum('acctoutputoctets') + $records->sum('acctinputoctets')),
        ];
    }

    public function disconnectUser(string $username): bool
    {
        try {
            Radacct::where('username', $username)
                ->whereNull('acctstoptime')
                ->update([
                    'acctstoptime' => now(),
                    'acctterminatecause' => 'Admin-Reset',
                ]);

            Log::info("RadiusService: Disconnected user {$username}");
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to disconnect user {$username}: " . $e->getMessage());
            return false;
        }
    }

    public function disconnectAllSessionsForNas(string $nasIpAddress): int
    {
        try {
            $count = Radacct::where('nasipaddress', $nasIpAddress)
                ->whereNull('acctstoptime')
                ->update([
                    'acctstoptime' => now(),
                    'acctterminatecause' => 'NAS-Reboot',
                ]);

            Log::info("RadiusService: Disconnected {$count} sessions for NAS {$nasIpAddress}");
            return $count;
        } catch (\Exception $e) {
            Log::error("RadiusService: Failed to disconnect sessions for NAS {$nasIpAddress}: " . $e->getMessage());
            return 0;
        }
    }

    public function getAuthenticationLogs(string $username, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return Radpostauth::where('username', $username)
            ->orderBy('authdate', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentAuthenticationAttempts(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return Radpostauth::orderBy('authdate', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getFailedAuthenticationAttempts(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return Radpostauth::where('reply', 'LIKE', '%Reject%')
            ->orWhere('reply', 'LIKE', '%Fail%')
            ->orderBy('authdate', 'desc')
            ->limit($limit)
            ->get();
    }

    public function syncAllCustomers(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $customers = Customer::with(['servicePlan', 'nas', 'pppoeProfile'])
            ->where('status', '!=', 'deleted')
            ->get();

        foreach ($customers as $customer) {
            try {
                if ($this->syncCustomer($customer)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Gagal sync pelanggan: {$customer->username}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sync {$customer->username}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function syncAllVouchers(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $vouchers = Voucher::with(['servicePlan'])
            ->where('status', 'unused')
            ->get();

        foreach ($vouchers as $voucher) {
            try {
                if ($this->syncVoucher($voucher)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Gagal sync voucher: {$voucher->code}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sync {$voucher->code}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function syncAllServicePlans(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $plans = ServicePlan::where('is_active', true)->get();

        foreach ($plans as $plan) {
            try {
                if ($this->syncServicePlan($plan)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Gagal sync paket: {$plan->name}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sync {$plan->name}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function getRadiusStats(): array
    {
        return [
            'total_users' => Radcheck::select('username')->distinct()->count(),
            'total_groups' => Radusergroup::select('groupname')->distinct()->count(),
            'active_sessions' => Radacct::whereNull('acctstoptime')->count(),
            'today_authentications' => Radpostauth::whereDate('authdate', today())->count(),
            'today_successful_auth' => Radpostauth::whereDate('authdate', today())
                ->where('reply', 'Access-Accept')
                ->count(),
            'today_failed_auth' => Radpostauth::whereDate('authdate', today())
                ->where('reply', '!=', 'Access-Accept')
                ->count(),
        ];
    }

    protected function calculateSessionTimeout(int $validity, string $unit): int
    {
        switch ($unit) {
            case 'minutes':
                return $validity * 60;
            case 'hours':
                return $validity * 3600;
            case 'days':
                return $validity * 86400;
            case 'weeks':
                return $validity * 604800;
            case 'months':
                return $validity * 2592000;
            default:
                return $validity;
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    protected function formatSeconds(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d jam %d menit', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $secs);
        }
        return sprintf('%d detik', $secs);
    }
}
