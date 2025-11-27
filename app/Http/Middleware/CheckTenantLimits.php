<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantDatabaseManager;
use App\Services\TenantUsageService;

class CheckTenantLimits
{
    protected TenantUsageService $usageService;

    public function __construct(TenantUsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    public function handle(Request $request, Closure $next, string $resourceType = ''): Response
    {
        $tenant = TenantDatabaseManager::getTenant();

        if (!$tenant) {
            return $next($request);
        }

        if (!in_array($request->method(), ['POST', 'PUT'])) {
            return $next($request);
        }

        $limitCheck = $this->checkLimit($request, $tenant, $resourceType);

        if ($limitCheck !== true) {
            return $this->handleLimitExceeded($request, $limitCheck);
        }

        return $next($request);
    }

    protected function checkLimit(Request $request, $tenant, string $resourceType): bool|array
    {
        if (empty($resourceType)) {
            $resourceType = $this->detectResourceType($request);
        }

        switch ($resourceType) {
            case 'router':
            case 'nas':
                if (!$this->usageService->canAddRouter($tenant)) {
                    return [
                        'type' => 'router',
                        'message' => 'Anda telah mencapai batas maksimum router pada paket Anda.',
                        'remaining' => 0,
                        'current' => $this->usageService->getUsage($tenant)['routers'],
                        'limit' => $this->usageService->getLimits($tenant)['max_routers'],
                    ];
                }
                break;

            case 'customer':
                if (!$this->usageService->canAddCustomer($tenant)) {
                    return [
                        'type' => 'customer',
                        'message' => 'Anda telah mencapai batas maksimum pelanggan pada paket Anda.',
                        'remaining' => 0,
                        'current' => $this->usageService->getUsage($tenant)['customers'],
                        'limit' => $this->usageService->getLimits($tenant)['max_users'],
                    ];
                }
                break;

            case 'voucher':
                $quantity = $request->input('quantity', 1);
                if (!$this->usageService->canAddVoucher($tenant, (int) $quantity)) {
                    $remaining = $this->usageService->getRemainingVouchers($tenant);
                    return [
                        'type' => 'voucher',
                        'message' => $remaining > 0
                            ? "Anda hanya dapat membuat {$remaining} voucher lagi pada paket Anda."
                            : 'Anda telah mencapai batas maksimum voucher pada paket Anda.',
                        'remaining' => $remaining,
                        'requested' => $quantity,
                        'current' => $this->usageService->getUsage($tenant)['vouchers'],
                        'limit' => $this->usageService->getLimits($tenant)['max_vouchers'],
                    ];
                }
                break;
        }

        return true;
    }

    protected function detectResourceType(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'nas') || str_contains($path, 'router')) {
            return 'router';
        }

        if (str_contains($path, 'customer')) {
            return 'customer';
        }

        if (str_contains($path, 'voucher')) {
            return 'voucher';
        }

        return '';
    }

    protected function handleLimitExceeded(Request $request, array $limitInfo): Response
    {
        $upgradeMessage = 'Silakan upgrade paket Anda untuk menambah kapasitas.';
        $fullMessage = $limitInfo['message'] . ' ' . $upgradeMessage;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'limit_exceeded',
                'message' => $fullMessage,
                'data' => [
                    'resource_type' => $limitInfo['type'],
                    'current' => $limitInfo['current'] ?? 0,
                    'limit' => $limitInfo['limit'] ?? 0,
                    'remaining' => $limitInfo['remaining'] ?? 0,
                    'upgrade_url' => route('tenant.settings.subscription'),
                ],
            ], 403);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $fullMessage)
            ->with('limit_exceeded', [
                'type' => $limitInfo['type'],
                'upgrade_url' => route('tenant.settings.subscription'),
            ]);
    }
}
