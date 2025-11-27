<?php

namespace App\Services;

use App\Models\Tenant\Customer;
use App\Models\Tenant\ServicePlan;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Radcheck;
use App\Models\Tenant\Radreply;
use App\Models\Tenant\Radusergroup;
use App\Models\Tenant\Radgroupreply;
use App\Models\Tenant\Radacct;
use Carbon\Carbon;

class RadiusService
{
    public function syncCustomer(Customer $customer): bool
    {
        if (!$customer->servicePlan) {
            return false;
        }

        $plan = $customer->servicePlan;
        $username = $customer->username;
        $password = $customer->pppoe_password ?? $customer->password;

        Radcheck::setPassword($username, $password);

        if ($customer->expires_at) {
            $expiration = Carbon::parse($customer->expires_at)->format('d M Y H:i:s');
            Radcheck::setExpiration($username, $expiration);
        }

        if ($plan->simultaneous_use > 0) {
            Radcheck::setSimultaneousUse($username, $plan->simultaneous_use);
        }

        if ($plan->bandwidth_down && $plan->bandwidth_up) {
            Radreply::setBandwidth($username, $plan->bandwidth_down, $plan->bandwidth_up);
        }

        if ($customer->static_ip) {
            Radreply::setFramedIP($username, $customer->static_ip);
        }

        $groupName = 'plan-' . $plan->id;
        Radusergroup::assignGroup($username, $groupName);

        return true;
    }

    public function syncVoucher(Voucher $voucher): bool
    {
        if (!$voucher->servicePlan) {
            return false;
        }

        $plan = $voucher->servicePlan;
        $username = $voucher->username ?? $voucher->code;
        $password = $voucher->password ?? $voucher->code;

        Radcheck::setPassword($username, $password);

        if ($voucher->expires_at) {
            $expiration = Carbon::parse($voucher->expires_at)->format('d M Y H:i:s');
            Radcheck::setExpiration($username, $expiration);
        }

        if ($plan->simultaneous_use > 0) {
            Radcheck::setSimultaneousUse($username, $plan->simultaneous_use);
        }

        if ($plan->bandwidth_down && $plan->bandwidth_up) {
            Radreply::setBandwidth($username, $plan->bandwidth_down, $plan->bandwidth_up);
        }

        $groupName = 'plan-' . $plan->id;
        Radusergroup::assignGroup($username, $groupName);

        return true;
    }

    public function syncServicePlan(ServicePlan $plan): bool
    {
        $groupName = 'plan-' . $plan->id;

        if ($plan->bandwidth_down && $plan->bandwidth_up) {
            Radgroupreply::setGroupBandwidth($groupName, $plan->bandwidth_down, $plan->bandwidth_up);
        }

        return true;
    }

    public function removeCustomer(Customer $customer): bool
    {
        $username = $customer->username;
        
        Radcheck::removeUser($username);
        Radreply::removeUser($username);
        Radusergroup::removeUser($username);

        return true;
    }

    public function removeVoucher(Voucher $voucher): bool
    {
        $username = $voucher->username ?? $voucher->code;
        
        Radcheck::removeUser($username);
        Radreply::removeUser($username);
        Radusergroup::removeUser($username);

        return true;
    }

    public function getActiveSessionsForUser(string $username): \Illuminate\Database\Eloquent\Collection
    {
        return Radacct::active()->byUsername($username)->get();
    }

    public function getActiveSessionsForNas(string $nasIpAddress): \Illuminate\Database\Eloquent\Collection
    {
        return Radacct::active()->byNas($nasIpAddress)->get();
    }

    public function getUsageStats(string $username, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = Radacct::byUsername($username);

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
            'total_download' => $records->sum('acctoutputoctets'),
            'total_upload' => $records->sum('acctinputoctets'),
            'total_traffic' => $records->sum('acctoutputoctets') + $records->sum('acctinputoctets'),
        ];
    }

    public function disconnectUser(string $username): bool
    {
        return true;
    }
}
