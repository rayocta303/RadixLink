<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Radacct;
use App\Models\Tenant\Radcheck;
use App\Models\Tenant\Radreply;
use App\Models\Tenant\Radpostauth;
use App\Helpers\ChapHelper;
use App\Services\RadiusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RadiusRestController extends Controller
{
    protected RadiusService $radiusService;

    public function __construct(RadiusService $radiusService)
    {
        $this->radiusService = $radiusService;
    }

    public function handle(Request $request)
    {
        $action = $request->header('X-FreeRadius-Section') ?? $request->input('action');
        
        Log::debug('RadiusREST: Received request', [
            'action' => $action,
            'data' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            return match ($action) {
                'authenticate' => $this->authenticate($request),
                'authorize' => $this->authorize($request),
                'accounting' => $this->accounting($request),
                'post-auth' => $this->postAuth($request),
                default => $this->invalidAction($action),
            };
        } catch (\Exception $e) {
            Log::error('RadiusREST: Error processing request', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            return $this->radiusReply([
                'Reply-Message' => 'Internal server error',
            ], 500);
        }
    }

    protected function authenticate(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $chapPassword = $request->input('CHAPassword') ?? $request->input('chap_password');
        $chapChallenge = $request->input('CHAPchallenge') ?? $request->input('chap_challenge');

        if (empty($username)) {
            return $this->radiusReply([
                'control:Auth-Type' => 'Reject',
                'reply:Reply-Message' => 'Username is required',
            ], 401);
        }

        $isChap = !empty($chapPassword);
        $isVoucher = ($username === $password);
        $authenticated = false;
        $validatedPassword = null;

        if ($isChap) {
            try {
                $result = $this->verifyChapPassword($username, $chapPassword, $chapChallenge);
                if ($result['success']) {
                    $authenticated = true;
                    $validatedPassword = $result['password'];
                    $isVoucher = $result['is_voucher'] ?? false;
                }
            } catch (\Exception $e) {
                Log::error('RadiusREST: CHAP verification error', ['error' => $e->getMessage()]);
            }
        }

        if (!$authenticated) {
            if (!empty($username) && empty($password)) {
                $isVoucher = true;
                $validatedPassword = $username;
            } elseif (empty($password)) {
                return $this->radiusReply([
                    'control:Auth-Type' => 'Reject',
                    'reply:Reply-Message' => 'Password is required',
                ], 401);
            } else {
                $validatedPassword = $password;
            }
        }

        if ($isVoucher || $username === $validatedPassword) {
            $voucher = $this->findVoucher($username);
            if ($voucher) {
                return $this->radiusReply([
                    'control:Auth-Type' => 'Accept',
                    'control:Cleartext-Password' => $voucher->password ?? $voucher->code,
                ], 200);
            }
        }

        $customer = $this->findCustomer($username, $validatedPassword);
        if ($customer) {
            $storedPassword = $customer->pppoe_password ?? $customer->password;
            return $this->radiusReply([
                'control:Auth-Type' => 'Accept',
                'control:Cleartext-Password' => $storedPassword,
            ], 200);
        }

        return $this->radiusReply([
            'control:Auth-Type' => 'Reject',
            'reply:Reply-Message' => 'Username or Password is incorrect',
        ], 401);
    }

    protected function authorize(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $chapPassword = $request->input('CHAPassword') ?? $request->input('chap_password');
        $chapChallenge = $request->input('CHAPchallenge') ?? $request->input('chap_challenge');
        $nasIp = $request->input('NAS-IP-Address') ?? $request->input('nasipaddress');
        $nasId = $request->input('NAS-Identifier') ?? $request->input('nasid');

        if (empty($username)) {
            return $this->radiusReply([
                'control:Auth-Type' => 'Reject',
                'reply:Reply-Message' => 'Username is required',
            ], 401);
        }

        $isChap = !empty($chapPassword);
        $isVoucher = ($username === $password);
        $authenticatedUsername = $username;

        if ($isChap) {
            $result = $this->verifyChapPassword($username, $chapPassword, $chapChallenge);
            if ($result['success']) {
                $password = $result['password'];
                $isVoucher = $result['is_voucher'];
                $authenticatedUsername = $result['username'] ?? $username;
            } else {
                return $this->radiusReply([
                    'Reply-Message' => 'CHAP authentication failed',
                ], 401);
            }
        }

        if (!empty($username) && empty($password)) {
            $isVoucher = true;
            $password = $username;
        }

        $customer = Customer::where('username', $authenticatedUsername)
            ->orWhere('pppoe_username', $authenticatedUsername)
            ->where('status', 'active')
            ->first();

        if ($customer) {
            if (!$isChap) {
                $storedPassword = $customer->pppoe_password ?? $customer->password ?? '';
                if ($storedPassword !== $password && $customer->password !== $password) {
                    return $this->radiusReply([
                        'Reply-Message' => 'Invalid password',
                    ], 401);
                }
            }

            if (method_exists($customer, 'isExpired') && $customer->isExpired()) {
                return $this->radiusReply([
                    'Reply-Message' => 'Account has expired',
                ], 401);
            }

            if ($customer->expires_at && Carbon::parse($customer->expires_at)->isPast()) {
                return $this->radiusReply([
                    'Reply-Message' => 'Account has expired',
                ], 401);
            }

            return $this->generateRadiusResponse($customer);
        }

        if ($isVoucher || $username === $password) {
            return $this->processVoucherAuthorization($username);
        }

        return $this->radiusReply([
            'Reply-Message' => 'Account not found or expired',
        ], 401);
    }

    protected function accounting(Request $request)
    {
        $username = $request->input('username');
        $statusType = $request->input('Acct-Status-Type') ?? $request->input('acct_status_type');
        $sessionId = $request->input('Acct-Session-Id') ?? $request->input('acctsessionid');
        $nasIp = $request->input('NAS-IP-Address') ?? $request->input('nasipaddress');
        $nasId = $request->input('NAS-Identifier') ?? $request->input('nasid');

        if (empty($username)) {
            return $this->radiusReply([
                'Reply-Message' => 'Username is required',
            ], 200);
        }

        $realUsername = $this->getRealUsername($username);

        try {
            $acct = Radacct::where('username', $realUsername)
                ->where('acctsessionid', $sessionId)
                ->where('nasipaddress', $nasIp)
                ->first();

            if (!$acct) {
                $acct = new Radacct();
                $acct->username = $realUsername;
                $acct->acctsessionid = $sessionId;
                $acct->nasipaddress = $nasIp;
                $acct->nasportid = $request->input('NAS-Port-Id') ?? $request->input('nasportid');
                $acct->nasporttype = $request->input('NAS-Port-Type') ?? $request->input('nasporttype');
                $acct->acctstarttime = now();
                $acct->calledstationid = $request->input('Called-Station-Id') ?? $request->input('calledstationid');
                $acct->callingstationid = $request->input('Calling-Station-Id') ?? $request->input('callingstationid');
                $acct->framedipaddress = $request->input('Framed-IP-Address') ?? $request->input('framedipaddress');
                $acct->acctuniqueid = $request->input('Acct-Unique-Session-Id') ?? uniqid();
            }

            $inputOctets = (int) ($request->input('Acct-Input-Octets') ?? $request->input('acctinputoctets') ?? 0);
            $outputOctets = (int) ($request->input('Acct-Output-Octets') ?? $request->input('acctoutputoctets') ?? 0);
            $inputGigawords = (int) ($request->input('Acct-Input-Gigawords') ?? 0);
            $outputGigawords = (int) ($request->input('Acct-Output-Gigawords') ?? 0);
            $sessionTime = (int) ($request->input('Acct-Session-Time') ?? $request->input('acctsessiontime') ?? 0);

            $totalInput = $inputOctets + ($inputGigawords * 4294967296);
            $totalOutput = $outputOctets + ($outputGigawords * 4294967296);

            switch (strtolower($statusType)) {
                case 'start':
                    $acct->acctstarttime = now();
                    $acct->acctinputoctets = 0;
                    $acct->acctoutputoctets = 0;
                    $acct->acctsessiontime = 0;
                    break;

                case 'stop':
                    $acct->acctstoptime = now();
                    $acct->acctinputoctets = $totalInput;
                    $acct->acctoutputoctets = $totalOutput;
                    $acct->acctsessiontime = $sessionTime;
                    $acct->acctterminatecause = $request->input('Acct-Terminate-Cause') ?? 'User-Request';
                    break;

                case 'interim-update':
                case 'alive':
                    $acct->acctinputoctets = $totalInput;
                    $acct->acctoutputoctets = $totalOutput;
                    $acct->acctsessiontime = $sessionTime;
                    $acct->framedipaddress = $request->input('Framed-IP-Address') ?? $acct->framedipaddress;
                    break;
            }

            $acct->save();

            Log::info('RadiusREST: Accounting recorded', [
                'username' => $realUsername,
                'status_type' => $statusType,
                'session_id' => $sessionId,
            ]);

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('RadiusREST: Accounting error', [
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 200);
        }
    }

    protected function postAuth(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $reply = $request->input('reply') ?? 'Access-Accept';
        $nasIp = $request->input('NAS-IP-Address') ?? $request->input('nasipaddress');

        try {
            Radpostauth::create([
                'username' => $username,
                'pass' => substr($password ?? '', 0, 64),
                'reply' => $reply,
                'authdate' => now(),
                'nasipaddress' => $nasIp,
            ]);

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('RadiusREST: Post-auth error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 200);
        }
    }

    protected function verifyChapPassword(string $username, string $chapPassword, ?string $chapChallenge): array
    {
        $customer = Customer::where('username', $username)
            ->orWhere('pppoe_username', $username)
            ->where('status', 'active')
            ->first();

        if ($customer) {
            $passwords = array_filter([
                $customer->password,
                $customer->pppoe_password,
            ]);

            foreach ($passwords as $password) {
                if (ChapHelper::verify($password, $chapPassword, $chapChallenge)) {
                    return [
                        'success' => true,
                        'password' => $password,
                        'is_voucher' => false,
                        'username' => $customer->username,
                    ];
                }
            }
        }

        if (ChapHelper::verify($username, $chapPassword, $chapChallenge)) {
            return [
                'success' => true,
                'password' => $username,
                'is_voucher' => true,
                'username' => $username,
            ];
        }

        if (ChapHelper::verify('', $chapPassword, $chapChallenge)) {
            return [
                'success' => true,
                'password' => $username,
                'is_voucher' => true,
                'username' => $username,
            ];
        }

        return ['success' => false];
    }

    protected function findCustomer(string $username, ?string $password): ?Customer
    {
        $customer = Customer::where(function ($query) use ($username) {
            $query->where('username', $username)
                  ->orWhere('pppoe_username', $username);
        })->where('status', 'active')->first();

        if (!$customer) {
            return null;
        }

        $storedPassword = $customer->pppoe_password ?? $customer->password;
        
        if ($storedPassword === $password) {
            return $customer;
        }

        return null;
    }

    protected function findVoucher(string $code): ?Voucher
    {
        return Voucher::where('code', $code)
            ->where('status', 'unused')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    protected function processVoucherAuthorization(string $code)
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return $this->radiusReply([
                'Reply-Message' => 'Invalid voucher code',
            ], 401);
        }

        if ($voucher->status === 'used' && $voucher->type === 'single') {
            return $this->radiusReply([
                'Reply-Message' => 'Voucher has been used',
            ], 401);
        }

        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            return $this->radiusReply([
                'Reply-Message' => 'Voucher has expired',
            ], 401);
        }

        if ($voucher->status === 'unused') {
            $this->activateVoucher($voucher);
        }

        return $this->generateVoucherRadiusResponse($voucher);
    }

    protected function activateVoucher(Voucher $voucher): void
    {
        $plan = $voucher->servicePlan;

        if ($plan && $plan->validity && $plan->validity_unit) {
            $expiresAt = $this->calculateExpiration($plan->validity, $plan->validity_unit);
            $voucher->expires_at = $expiresAt;
        }

        $voucher->status = 'active';
        $voucher->activated_at = now();
        $voucher->save();

        $this->radiusService->syncVoucher($voucher);

        Log::info('RadiusREST: Voucher activated', [
            'code' => $voucher->code,
            'expires_at' => $voucher->expires_at,
        ]);
    }

    protected function calculateExpiration(int $validity, string $unit): Carbon
    {
        return match ($unit) {
            'minutes', 'Mins' => now()->addMinutes($validity),
            'hours', 'Hrs' => now()->addHours($validity),
            'days', 'Days' => now()->addDays($validity),
            'weeks' => now()->addWeeks($validity),
            'months', 'Months' => now()->addMonths($validity),
            default => now()->addDays($validity),
        };
    }

    protected function generateRadiusResponse(Customer $customer)
    {
        $plan = $customer->servicePlan;
        $response = [];

        $response['control:Auth-Type'] = [
            'value' => ['Accept'],
            'op' => ':=',
        ];

        if ($plan) {
            $rateLimit = $plan->getMikrotikRateLimit();
            if ($rateLimit) {
                $response['reply:Mikrotik-Rate-Limit'] = [
                    'value' => [$rateLimit],
                    'op' => ':=',
                ];
            }

            if ($plan->simultaneous_use) {
                $response['config:Simultaneous-Use'] = [
                    'value' => [(string) $plan->simultaneous_use],
                    'op' => ':=',
                ];
            }

            if ($plan->validity && $plan->validity_unit) {
                $timeout = $plan->validity_in_seconds ?? 0;
                if ($timeout > 0) {
                    $response['reply:Session-Timeout'] = [
                        'value' => [(string) $timeout],
                        'op' => ':=',
                    ];
                }
            }

            if ($customer->pppoeProfile && $customer->pppoeProfile->idle_timeout) {
                $response['reply:Idle-Timeout'] = [
                    'value' => [(string) $customer->pppoeProfile->idle_timeout],
                    'op' => ':=',
                ];
            }

            if ($customer->pppoeProfile && $customer->pppoeProfile->address_list) {
                $response['reply:Mikrotik-Address-List'] = [
                    'value' => [$customer->pppoeProfile->address_list],
                    'op' => ':=',
                ];
            }

            if ($plan->ipPool && $plan->ipPool->pool_name) {
                $response['reply:Framed-Pool'] = [
                    'value' => [$plan->ipPool->pool_name],
                    'op' => ':=',
                ];
            }
        }

        if ($customer->static_ip) {
            $response['reply:Framed-IP-Address'] = [
                'value' => [$customer->static_ip],
                'op' => ':=',
            ];
        }

        $response['reply:Reply-Message'] = [
            'value' => ['Authorization accepted'],
            'op' => ':=',
        ];

        Log::info('RadiusREST: Authorization granted for customer', [
            'username' => $customer->username,
            'attributes' => array_keys($response),
        ]);

        return response()->json($response, 200);
    }

    protected function generateVoucherRadiusResponse(Voucher $voucher)
    {
        $plan = $voucher->servicePlan;
        $response = [];

        $response['control:Auth-Type'] = [
            'value' => ['Accept'],
            'op' => ':=',
        ];

        if ($plan) {
            $rateLimit = $plan->getMikrotikRateLimit();
            if ($rateLimit) {
                $response['reply:Mikrotik-Rate-Limit'] = [
                    'value' => [$rateLimit],
                    'op' => ':=',
                ];
            }

            if ($plan->simultaneous_use) {
                $response['config:Simultaneous-Use'] = [
                    'value' => [(string) $plan->simultaneous_use],
                    'op' => ':=',
                ];
            }

            if ($plan->validity && $plan->validity_unit) {
                $validitySeconds = $plan->validity_in_seconds ?? 0;
                if ($validitySeconds > 0) {
                    $response['reply:Session-Timeout'] = [
                        'value' => [(string) $validitySeconds],
                        'op' => ':=',
                    ];
                }
            }

            if ($plan->quota_bytes) {
                $response['reply:Mikrotik-Total-Limit'] = [
                    'value' => [(string) $plan->quota_bytes],
                    'op' => ':=',
                ];
            }

            if ($plan->hotspotProfile) {
                if ($plan->hotspotProfile->idle_timeout) {
                    $response['reply:Idle-Timeout'] = [
                        'value' => [(string) $plan->hotspotProfile->idle_timeout],
                        'op' => ':=',
                    ];
                }
                if ($plan->hotspotProfile->address_list) {
                    $response['reply:Mikrotik-Address-List'] = [
                        'value' => [$plan->hotspotProfile->address_list],
                        'op' => ':=',
                    ];
                }
            }

            if ($plan->ipPool && $plan->ipPool->pool_name) {
                $response['reply:Framed-Pool'] = [
                    'value' => [$plan->ipPool->pool_name],
                    'op' => ':=',
                ];
            }
        }

        $response['reply:Reply-Message'] = [
            'value' => ['Authorization accepted'],
            'op' => ':=',
        ];

        Log::info('RadiusREST: Authorization granted for voucher', [
            'code' => $voucher->code,
            'attributes' => array_keys($response),
        ]);

        return response()->json($response, 200);
    }

    protected function formatMikrotikTime(int $validity, string $unit): string
    {
        return match ($unit) {
            'minutes', 'Mins' => "{$validity}m",
            'hours', 'Hrs' => "{$validity}h",
            'days', 'Days' => "{$validity}d",
            'weeks' => ($validity * 7) . 'd',
            'months', 'Months' => ($validity * 30) . 'd',
            default => "{$validity}s",
        };
    }

    protected function getRealUsername(string $username): string
    {
        $customer = Customer::where('pppoe_username', $username)->first();
        
        if ($customer) {
            return $customer->username;
        }

        return $username;
    }

    protected function radiusReply(array $attributes, int $statusCode = 200)
    {
        $formattedReply = [];

        foreach ($attributes as $key => $value) {
            $attrKey = str_contains($key, ':') ? $key : 'reply:' . $key;
            $formattedReply[$attrKey] = [
                'value' => [(string) $value],
                'op' => ':=',
            ];
        }

        if ($statusCode === 200 && !isset($formattedReply['control:Auth-Type'])) {
            $formattedReply['control:Auth-Type'] = [
                'value' => ['Accept'],
                'op' => ':=',
            ];
        }

        if ($statusCode === 200 && !isset($formattedReply['reply:Reply-Message'])) {
            $formattedReply['reply:Reply-Message'] = [
                'value' => ['OK'],
                'op' => ':=',
            ];
        }

        return response()->json($formattedReply, $statusCode);
    }

    protected function formatRadiusAttributes(array $attributes): array
    {
        $formatted = [];
        
        foreach ($attributes as $key => $value) {
            $formatted[$key] = [
                'value' => [(string) $value],
                'op' => ':=',
            ];
        }

        return $formatted;
    }

    protected function invalidAction(?string $action)
    {
        Log::warning('RadiusREST: Invalid action received', ['action' => $action]);
        
        return response()->json([
            'error' => 'Invalid action',
            'message' => "Action '{$action}' is not supported",
        ], 400);
    }
}
