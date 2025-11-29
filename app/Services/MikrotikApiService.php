<?php

namespace App\Services;

use App\Models\Tenant\Nas;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\ServicePlan;
use App\Models\Tenant\IpPool;
use App\Models\Tenant\BandwidthProfile;
use App\Models\Tenant\PppoeProfile;
use App\Models\Tenant\HotspotProfile;
use Illuminate\Support\Facades\Log;

class MikrotikApiService
{
    protected ?Nas $nas = null;
    protected $socket = null;
    protected bool $connected = false;
    protected int $timeout = 5;

    public function setNas(Nas $nas): self
    {
        $this->nas = $nas;
        return $this;
    }

    public function connect(): bool
    {
        if (!$this->nas) {
            Log::error('MikrotikApiService: No NAS configured');
            return false;
        }

        if ($this->connected) {
            return true;
        }

        try {
            $this->socket = @fsockopen(
                $this->nas->nasname,
                $this->nas->api_port ?? 8728,
                $errno,
                $errstr,
                $this->timeout
            );

            if (!$this->socket) {
                Log::error("MikrotikApiService: Connection failed - {$errstr} ({$errno})");
                return false;
            }

            stream_set_timeout($this->socket, $this->timeout);

            if ($this->login()) {
                $this->connected = true;
                $this->nas->update(['last_seen' => now()]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('MikrotikApiService: ' . $e->getMessage());
            return false;
        }
    }

    protected function login(): bool
    {
        if (!$this->socket) {
            return false;
        }

        $username = $this->nas->api_username ?? 'admin';
        $password = $this->nas->api_password ?? '';

        $response = $this->sendCommand(['/login', '=name=' . $username, '=password=' . $password]);

        if (isset($response[0]) && $response[0] === '!done') {
            return true;
        }

        if (isset($response[1]) && strpos($response[1], '=ret=') !== false) {
            $challenge = substr($response[1], 5);
            $hash = md5(chr(0) . $password . pack('H*', $challenge), true);
            $response = $this->sendCommand(['/login', '=name=' . $username, '=response=00' . bin2hex($hash)]);
            return isset($response[0]) && $response[0] === '!done';
        }

        return false;
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            $this->sendCommand(['/quit']);
            fclose($this->socket);
            $this->socket = null;
        }
        $this->connected = false;
    }

    protected function sendCommand(array $commands): array
    {
        if (!$this->socket) {
            return [];
        }

        foreach ($commands as $command) {
            $this->writeWord($command);
        }
        $this->writeWord('');

        return $this->readResponse();
    }

    protected function writeWord(string $word): void
    {
        $len = strlen($word);
        
        if ($len < 0x80) {
            fwrite($this->socket, chr($len));
        } elseif ($len < 0x4000) {
            $len |= 0x8000;
            fwrite($this->socket, chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        } elseif ($len < 0x200000) {
            $len |= 0xC00000;
            fwrite($this->socket, chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        } elseif ($len < 0x10000000) {
            $len |= 0xE0000000;
            fwrite($this->socket, chr(($len >> 24) & 0xFF) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0) . chr(($len >> 24) & 0xFF) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        }

        fwrite($this->socket, $word);
    }

    protected function readWord(): string
    {
        $byte = ord(fread($this->socket, 1));
        
        if ($byte & 0x80) {
            if (($byte & 0xC0) == 0x80) {
                $len = (($byte & ~0xC0) << 8) + ord(fread($this->socket, 1));
            } elseif (($byte & 0xE0) == 0xC0) {
                $len = (($byte & ~0xE0) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } elseif (($byte & 0xF0) == 0xE0) {
                $len = (($byte & ~0xF0) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } elseif ($byte == 0xF0) {
                $len = (ord(fread($this->socket, 1)) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            }
        } else {
            $len = $byte;
        }

        return $len > 0 ? fread($this->socket, $len) : '';
    }

    protected function readResponse(): array
    {
        $response = [];
        
        while (true) {
            $word = $this->readWord();
            
            if ($word === '') {
                if (isset($response[0]) && in_array($response[0], ['!done', '!trap', '!fatal'])) {
                    break;
                }
                continue;
            }
            
            $response[] = $word;
            
            if ($word === '!done' || $word === '!fatal') {
                $this->readWord();
                break;
            }
        }

        return $response;
    }

    public function getIdentity(): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand(['/system/identity/print']);
        
        return $this->parseResponse($response);
    }

    public function getSystemResource(): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand(['/system/resource/print']);
        
        return $this->parseResponse($response);
    }

    public function getInterfaces(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $response = $this->sendCommand(['/interface/print']);
        
        return $this->parseMultipleResponse($response);
    }

    public function getActiveConnections(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $pppoe = $this->sendCommand(['/ppp/active/print']);
        $hotspot = $this->sendCommand(['/ip/hotspot/active/print']);
        
        return [
            'pppoe' => $this->parseMultipleResponse($pppoe),
            'hotspot' => $this->parseMultipleResponse($hotspot),
        ];
    }

    public function getHotspotUsers(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $response = $this->sendCommand(['/ip/hotspot/user/print']);
        
        return $this->parseMultipleResponse($response);
    }

    public function getPppSecrets(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $response = $this->sendCommand(['/ppp/secret/print']);
        
        return $this->parseMultipleResponse($response);
    }

    public function addPppSecret(Customer $customer): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $plan = $customer->servicePlan;
        $profile = $customer->pppoeProfile;
        
        $command = [
            '/ppp/secret/add',
            '=name=' . $customer->username,
            '=password=' . ($customer->pppoe_password ?? 'password123'),
            '=service=pppoe',
        ];

        if ($profile) {
            $command[] = '=profile=' . $profile->profile_name;
        }

        if ($customer->static_ip) {
            $command[] = '=remote-address=' . $customer->static_ip;
        }

        if ($plan && $plan->bandwidth_down && $plan->bandwidth_up) {
            $command[] = '=rate-limit=' . $plan->bandwidth_up . '/' . $plan->bandwidth_down;
        }

        $command[] = '=comment=' . $customer->name;

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function updatePppSecret(Customer $customer): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/secret/print',
            '?name=' . $customer->username,
        ]);

        $secrets = $this->parseMultipleResponse($findResponse);
        
        if (empty($secrets)) {
            return $this->addPppSecret($customer);
        }

        $secretId = $secrets[0]['.id'] ?? null;
        
        if (!$secretId) {
            return false;
        }

        $plan = $customer->servicePlan;
        $profile = $customer->pppoeProfile;

        $command = [
            '/ppp/secret/set',
            '=.id=' . $secretId,
            '=password=' . ($customer->pppoe_password ?? 'password123'),
        ];

        if ($profile) {
            $command[] = '=profile=' . $profile->profile_name;
        }

        if ($customer->static_ip) {
            $command[] = '=remote-address=' . $customer->static_ip;
        }

        if ($plan && $plan->bandwidth_down && $plan->bandwidth_up) {
            $command[] = '=rate-limit=' . $plan->bandwidth_up . '/' . $plan->bandwidth_down;
        }

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function removePppSecret(string $username): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/secret/print',
            '?name=' . $username,
        ]);

        $secrets = $this->parseMultipleResponse($findResponse);
        
        if (empty($secrets)) {
            return true;
        }

        $secretId = $secrets[0]['.id'] ?? null;
        
        if (!$secretId) {
            return false;
        }

        $response = $this->sendCommand([
            '/ppp/secret/remove',
            '=.id=' . $secretId,
        ]);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function addHotspotUser(Voucher $voucher): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $plan = $voucher->servicePlan;
        $profile = $voucher->hotspotProfile ?? $plan->hotspotProfile ?? null;
        
        $command = [
            '/ip/hotspot/user/add',
            '=name=' . ($voucher->username ?? $voucher->code),
            '=password=' . ($voucher->password ?? $voucher->code),
            '=server=all',
        ];

        if ($profile) {
            $command[] = '=profile=' . $profile->profile_name;
        }

        if ($plan && $plan->validity && $plan->validity_unit) {
            $uptime = $this->calculateUptime($plan->validity, $plan->validity_unit);
            $command[] = '=limit-uptime=' . $uptime;
        }

        if ($plan && $plan->quota_bytes) {
            $command[] = '=limit-bytes-total=' . $plan->quota_bytes;
        }

        $command[] = '=comment=Voucher: ' . $voucher->code;

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function removeHotspotUser(string $username): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ip/hotspot/user/print',
            '?name=' . $username,
        ]);

        $users = $this->parseMultipleResponse($findResponse);
        
        if (empty($users)) {
            return true;
        }

        $userId = $users[0]['.id'] ?? null;
        
        if (!$userId) {
            return false;
        }

        $response = $this->sendCommand([
            '/ip/hotspot/user/remove',
            '=.id=' . $userId,
        ]);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function disconnectPppUser(string $username): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/active/print',
            '?name=' . $username,
        ]);

        $active = $this->parseMultipleResponse($findResponse);
        
        if (empty($active)) {
            return true;
        }

        $activeId = $active[0]['.id'] ?? null;
        
        if (!$activeId) {
            return false;
        }

        $response = $this->sendCommand([
            '/ppp/active/remove',
            '=.id=' . $activeId,
        ]);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function disconnectHotspotUser(string $username): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ip/hotspot/active/print',
            '?user=' . $username,
        ]);

        $active = $this->parseMultipleResponse($findResponse);
        
        if (empty($active)) {
            return true;
        }

        $activeId = $active[0]['.id'] ?? null;
        
        if (!$activeId) {
            return false;
        }

        $response = $this->sendCommand([
            '/ip/hotspot/active/remove',
            '=.id=' . $activeId,
        ]);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function addIpPool(IpPool $pool): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ip/pool/add',
            '=name=' . $pool->pool_name,
            '=ranges=' . $pool->range_start . '-' . $pool->range_end,
        ];

        if ($pool->next_pool) {
            $command[] = '=next-pool=' . $pool->next_pool;
        }

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function addPppProfile(PppoeProfile $profile): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ppp/profile/add',
            '=name=' . $profile->profile_name,
        ];

        if ($profile->local_address) {
            $command[] = '=local-address=' . $profile->local_address;
        }

        if ($profile->remote_address) {
            $command[] = '=remote-address=' . $profile->remote_address;
        }

        if ($profile->dns_server) {
            $command[] = '=dns-server=' . $profile->dns_server;
        }

        if ($profile->bandwidth) {
            $command[] = '=rate-limit=' . $profile->bandwidth->rate_up . '/' . $profile->bandwidth->rate_down;
        }

        if ($profile->address_list) {
            $command[] = '=address-list=' . $profile->address_list;
        }

        if ($profile->session_timeout) {
            $command[] = '=session-timeout=' . $profile->session_timeout . 's';
        }

        if ($profile->idle_timeout) {
            $command[] = '=idle-timeout=' . $profile->idle_timeout . 's';
        }

        $onlyOne = $profile->only_one ? 'yes' : 'no';
        $command[] = '=only-one=' . $onlyOne;

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function addHotspotProfile(HotspotProfile $profile): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ip/hotspot/user/profile/add',
            '=name=' . $profile->profile_name,
            '=shared-users=' . ($profile->shared_users ?? 1),
        ];

        if ($profile->bandwidth) {
            $command[] = '=rate-limit=' . $profile->bandwidth->rate_up . '/' . $profile->bandwidth->rate_down;
        }

        if ($profile->session_timeout) {
            $command[] = '=session-timeout=' . $profile->session_timeout . 's';
        }

        if ($profile->idle_timeout) {
            $command[] = '=idle-timeout=' . $profile->idle_timeout . 's';
        }

        if ($profile->keepalive_timeout) {
            $command[] = '=keepalive-timeout=' . $profile->keepalive_timeout . 's';
        }

        if ($profile->status_autorefresh) {
            $command[] = '=status-autorefresh=' . $profile->status_autorefresh;
        }

        if ($profile->mac_cookie_timeout) {
            $command[] = '=mac-cookie-timeout=' . $profile->mac_cookie_timeout;
        }

        if ($profile->address_list) {
            $command[] = '=address-list=' . $profile->address_list;
        }

        $transparentProxy = $profile->transparent_proxy ? 'yes' : 'no';
        $command[] = '=transparent-proxy=' . $transparentProxy;

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function addSimpleQueue(Customer $customer): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $plan = $customer->servicePlan;
        
        if (!$plan) {
            return false;
        }

        $command = [
            '/queue/simple/add',
            '=name=' . $customer->username,
            '=target=' . ($customer->static_ip ?? '0.0.0.0/0'),
            '=max-limit=' . ($plan->bandwidth_up ?? '5M') . '/' . ($plan->bandwidth_down ?? '10M'),
            '=comment=' . $customer->name,
        ];

        if ($plan->burst_limit) {
            $command[] = '=burst-limit=' . $plan->burst_limit;
        }

        $response = $this->sendCommand($command);
        
        return isset($response[0]) && $response[0] === '!done';
    }

    public function getQueueStats(string $queueName): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand([
            '/queue/simple/print',
            '?name=' . $queueName,
        ]);

        $queues = $this->parseMultipleResponse($response);
        
        return $queues[0] ?? null;
    }

    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'identity' => null,
            'resource' => null,
        ];

        if (!$this->nas) {
            $result['message'] = 'NAS tidak dikonfigurasi';
            return $result;
        }

        if (!$this->connect()) {
            $result['message'] = 'Gagal terhubung ke router. Periksa IP, port, username, dan password.';
            return $result;
        }

        $identity = $this->getIdentity();
        $resource = $this->getSystemResource();

        $result['success'] = true;
        $result['message'] = 'Koneksi berhasil';
        $result['identity'] = $identity;
        $result['resource'] = $resource;

        $this->disconnect();

        return $result;
    }

    protected function parseResponse(array $response): ?array
    {
        $result = [];
        
        foreach ($response as $item) {
            if (strpos($item, '=') === 0) {
                $parts = explode('=', substr($item, 1), 2);
                if (count($parts) === 2) {
                    $result[$parts[0]] = $parts[1];
                }
            }
        }

        return !empty($result) ? $result : null;
    }

    protected function parseMultipleResponse(array $response): array
    {
        $results = [];
        $currentItem = [];
        
        foreach ($response as $item) {
            if ($item === '!re') {
                if (!empty($currentItem)) {
                    $results[] = $currentItem;
                }
                $currentItem = [];
            } elseif (strpos($item, '=') === 0) {
                $parts = explode('=', substr($item, 1), 2);
                if (count($parts) === 2) {
                    $currentItem[$parts[0]] = $parts[1];
                }
            }
        }
        
        if (!empty($currentItem)) {
            $results[] = $currentItem;
        }

        return $results;
    }

    protected function calculateUptime(int $validity, string $unit): string
    {
        return match (strtolower($unit)) {
            'minutes', 'mins' => $validity . 'm',
            'hours', 'hrs' => $validity . 'h',
            'days' => $validity . 'd',
            'weeks' => ($validity * 7) . 'd',
            'months' => ($validity * 30) . 'd',
            default => $validity . 's',
        };
    }

    public function isUserLoggedIn(string $username): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $response = $this->sendCommand([
            '/ip/hotspot/active/print',
            '?user=' . $username,
        ]);

        $active = $this->parseMultipleResponse($response);

        if (!empty($active)) {
            return true;
        }

        $pppResponse = $this->sendCommand([
            '/ppp/active/print',
            '?name=' . $username,
        ]);

        $pppActive = $this->parseMultipleResponse($pppResponse);

        return !empty($pppActive);
    }

    public function loginHotspotUser(string $username, string $password, string $ip, string $mac): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ip/hotspot/active/login',
            '=user=' . $username,
            '=password=' . $password,
            '=ip=' . $ip,
            '=mac-address=' . $mac,
        ];

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setHotspotUserProfile(string $username, string $profileName): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ip/hotspot/user/print',
            '.proplist=.id',
            '?name=' . $username,
        ]);

        $users = $this->parseMultipleResponse($findResponse);

        if (empty($users)) {
            return false;
        }

        $userId = $users[0]['.id'] ?? null;

        if (!$userId) {
            return false;
        }

        $command = [
            '/ip/hotspot/user/set',
            '=.id=' . $userId,
            '=profile=' . $profileName,
        ];

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setHotspotUserPassword(string $username, string $password): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ip/hotspot/user/print',
            '.proplist=.id',
            '?name=' . $username,
        ]);

        $users = $this->parseMultipleResponse($findResponse);

        if (empty($users)) {
            return false;
        }

        $userId = $users[0]['.id'] ?? null;

        if (!$userId) {
            return false;
        }

        $command = [
            '/ip/hotspot/user/set',
            '=.id=' . $userId,
            '=password=' . $password,
        ];

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setPppoeUserProfile(string $username, string $profileName): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/secret/print',
            '.proplist=.id',
            '?name=' . $username,
        ]);

        $secrets = $this->parseMultipleResponse($findResponse);

        if (empty($secrets)) {
            return false;
        }

        $secretId = $secrets[0]['.id'] ?? null;

        if (!$secretId) {
            return false;
        }

        $command = [
            '/ppp/secret/set',
            '=.id=' . $secretId,
            '=profile=' . $profileName,
        ];

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setPppoeUserPassword(string $username, string $password): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/secret/print',
            '.proplist=.id',
            '?name=' . $username,
        ]);

        $secrets = $this->parseMultipleResponse($findResponse);

        if (empty($secrets)) {
            return false;
        }

        $secretId = $secrets[0]['.id'] ?? null;

        if (!$secretId) {
            return false;
        }

        $command = [
            '/ppp/secret/set',
            '=.id=' . $secretId,
            '=password=' . $password,
        ];

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setOrAddHotspotProfile(string $profileName, int $sharedUsers, string $rateLimit): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ip/hotspot/user/profile/print',
            '.proplist=.id',
            '?name=' . $profileName,
        ]);

        $profiles = $this->parseMultipleResponse($findResponse);

        if (empty($profiles)) {
            $command = [
                '/ip/hotspot/user/profile/add',
                '=name=' . $profileName,
                '=shared-users=' . $sharedUsers,
                '=rate-limit=' . $rateLimit,
            ];
        } else {
            $profileId = $profiles[0]['.id'] ?? null;
            if (!$profileId) {
                Log::warning('MikrotikApiService: Profile found but no .id', ['profile' => $profileName]);
                $command = [
                    '/ip/hotspot/user/profile/add',
                    '=name=' . $profileName,
                    '=shared-users=' . $sharedUsers,
                    '=rate-limit=' . $rateLimit,
                ];
            } else {
                $command = [
                    '/ip/hotspot/user/profile/set',
                    '=numbers=' . $profileId,
                    '=shared-users=' . $sharedUsers,
                    '=rate-limit=' . $rateLimit,
                ];
            }
        }

        $response = $this->sendCommand($command);

        if (isset($response[0]) && $response[0] === '!trap') {
            Log::warning('MikrotikApiService: Failed to set/add hotspot profile', [
                'profile' => $profileName,
                'response' => $response,
            ]);
            return false;
        }

        return isset($response[0]) && $response[0] === '!done';
    }

    public function setOrAddPppProfile(string $profileName, string $localAddress, string $remoteAddress, string $rateLimit): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $findResponse = $this->sendCommand([
            '/ppp/profile/print',
            '.proplist=.id',
            '?name=' . $profileName,
        ]);

        $profiles = $this->parseMultipleResponse($findResponse);

        if (empty($profiles)) {
            $command = [
                '/ppp/profile/add',
                '=name=' . $profileName,
                '=local-address=' . $localAddress,
                '=remote-address=' . $remoteAddress,
                '=rate-limit=' . $rateLimit,
            ];
        } else {
            $profileId = $profiles[0]['.id'] ?? null;
            if (!$profileId) {
                Log::warning('MikrotikApiService: PPP Profile found but no .id', ['profile' => $profileName]);
                $command = [
                    '/ppp/profile/add',
                    '=name=' . $profileName,
                    '=local-address=' . $localAddress,
                    '=remote-address=' . $remoteAddress,
                    '=rate-limit=' . $rateLimit,
                ];
            } else {
                $command = [
                    '/ppp/profile/set',
                    '=numbers=' . $profileId,
                    '=local-address=' . $localAddress,
                    '=remote-address=' . $remoteAddress,
                    '=rate-limit=' . $rateLimit,
                ];
            }
        }

        $response = $this->sendCommand($command);

        if (isset($response[0]) && $response[0] === '!trap') {
            Log::warning('MikrotikApiService: Failed to set/add PPP profile', [
                'profile' => $profileName,
                'response' => $response,
            ]);
            return false;
        }

        return isset($response[0]) && $response[0] === '!done';
    }

    public function addHotspotUserWithLimits(
        string $username,
        string $password,
        string $profileName,
        ?string $comment = null,
        ?string $limitUptime = null,
        int|string|null $limitBytesTotal = null,
        ?string $email = null
    ): bool {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ip/hotspot/user/add',
            '=name=' . $username,
            '=password=' . $password,
            '=profile=' . $profileName,
        ];

        if ($comment) {
            $command[] = '=comment=' . $comment;
        }

        if ($email) {
            $command[] = '=email=' . $email;
        }

        if ($limitUptime) {
            $command[] = '=limit-uptime=' . $limitUptime;
        }

        if ($limitBytesTotal !== null) {
            $bytesValue = is_numeric($limitBytesTotal) ? (int) $limitBytesTotal : $this->parseBytesString($limitBytesTotal);
            $command[] = '=limit-bytes-total=' . $bytesValue;
        }

        $response = $this->sendCommand($command);

        if (isset($response[0]) && $response[0] === '!trap') {
            Log::warning('MikrotikApiService: Failed to add hotspot user', [
                'username' => $username,
                'response' => $response,
            ]);
            return false;
        }

        return isset($response[0]) && $response[0] === '!done';
    }

    protected function parseBytesString(string $value): int
    {
        $value = strtoupper(trim($value));
        $numeric = (float) preg_replace('/[^0-9.]/', '', $value);

        if (str_contains($value, 'G')) {
            return (int) ($numeric * 1073741824);
        } elseif (str_contains($value, 'M')) {
            return (int) ($numeric * 1048576);
        } elseif (str_contains($value, 'K')) {
            return (int) ($numeric * 1024);
        }

        return (int) $numeric;
    }

    public function addPppSecretWithProfile(
        string $username,
        string $password,
        string $profileName,
        ?string $comment = null,
        ?string $remoteAddress = null
    ): bool {
        if (!$this->connect()) {
            return false;
        }

        $command = [
            '/ppp/secret/add',
            '=name=' . $username,
            '=password=' . $password,
            '=service=pppoe',
            '=profile=' . $profileName,
        ];

        if ($comment) {
            $command[] = '=comment=' . $comment;
        }

        if ($remoteAddress) {
            $command[] = '=remote-address=' . $remoteAddress;
        }

        $response = $this->sendCommand($command);

        return isset($response[0]) && $response[0] === '!done';
    }

    public function enableRadiusForHotspot(string $radiusServer, string $radiusSecret, int $authPort = 1812, int $acctPort = 1813): bool
    {
        if (!$this->connect()) {
            return false;
        }

        $this->sendCommand([
            '/radius/add',
            '=address=' . $radiusServer,
            '=secret=' . $radiusSecret,
            '=service=hotspot,ppp,login',
            '=authentication-port=' . $authPort,
            '=accounting-port=' . $acctPort,
        ]);

        $response = $this->sendCommand([
            '/radius/incoming/set',
            '=accept=yes',
            '=port=3799',
        ]);

        return true;
    }

    public function getHotspotActiveUser(string $username): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand([
            '/ip/hotspot/active/print',
            '?user=' . $username,
        ]);

        $active = $this->parseMultipleResponse($response);

        return $active[0] ?? null;
    }

    public function getPppActiveUser(string $username): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand([
            '/ppp/active/print',
            '?name=' . $username,
        ]);

        $active = $this->parseMultipleResponse($response);

        return $active[0] ?? null;
    }

    public function getHotspotUserStats(string $username): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        $response = $this->sendCommand([
            '/ip/hotspot/user/print',
            '?name=' . $username,
        ]);

        $users = $this->parseMultipleResponse($response);

        return $users[0] ?? null;
    }

    public function formatBytesForMikrotik(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824) . 'G';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576) . 'M';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024) . 'K';
        }
        return (string) $bytes;
    }

    public function formatTimeForMikrotik(int $seconds): string
    {
        if ($seconds >= 86400) {
            $days = floor($seconds / 86400);
            $remainder = $seconds % 86400;
            $hours = floor($remainder / 3600);
            if ($hours > 0) {
                return "{$days}d{$hours}h";
            }
            return "{$days}d";
        } elseif ($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            if ($minutes > 0) {
                return "{$hours}h{$minutes}m";
            }
            return "{$hours}h";
        } elseif ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            return "{$minutes}m";
        }
        return "{$seconds}s";
    }

    public function syncAllCustomers(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if (!$this->connect()) {
            $results['errors'][] = 'Gagal terhubung ke router';
            return $results;
        }

        $customers = Customer::with(['servicePlan', 'pppoeProfile'])
            ->where('status', 'active')
            ->where('service_type', 'pppoe')
            ->get();

        foreach ($customers as $customer) {
            try {
                if ($this->addPppSecret($customer)) {
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

        $this->disconnect();

        return $results;
    }

    public function syncAllVouchers(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if (!$this->connect()) {
            $results['errors'][] = 'Gagal terhubung ke router';
            return $results;
        }

        $vouchers = Voucher::with(['servicePlan', 'hotspotProfile'])
            ->where('status', 'unused')
            ->get();

        foreach ($vouchers as $voucher) {
            try {
                if ($this->addHotspotUser($voucher)) {
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

        $this->disconnect();

        return $results;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
