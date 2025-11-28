<?php

namespace App\Http\Middleware;

use App\Services\TenantUsageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    public function __construct(
        protected TenantUsageService $usageService
    ) {}

    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return $this->limitExceededResponse('Tenant tidak ditemukan.');
        }

        $canAdd = match ($resource) {
            'router', 'routers' => $this->usageService->canAddRouter($tenant),
            'customer', 'customers' => $this->usageService->canAddCustomer($tenant),
            'voucher', 'vouchers' => $this->usageService->canAddVoucher($tenant),
            'online_user', 'online_users' => $this->usageService->canAddOnlineUser($tenant),
            default => true,
        };

        if (!$canAdd) {
            $message = $this->getLimitMessage($resource);
            return $this->limitExceededResponse($message, $resource);
        }

        return $next($request);
    }

    protected function getLimitMessage(string $resource): string
    {
        return match ($resource) {
            'router', 'routers' => 'Anda telah mencapai batas maksimum router. Silakan upgrade paket langganan untuk menambah kapasitas.',
            'customer', 'customers' => 'Anda telah mencapai batas maksimum pelanggan. Silakan upgrade paket langganan untuk menambah kapasitas.',
            'voucher', 'vouchers' => 'Anda telah mencapai batas maksimum voucher. Silakan upgrade paket langganan untuk menambah kapasitas.',
            'online_user', 'online_users' => 'Anda telah mencapai batas maksimum pengguna online. Silakan upgrade paket langganan untuk menambah kapasitas.',
            default => 'Anda telah mencapai batas maksimum resource ini.',
        };
    }

    protected function limitExceededResponse(string $message, ?string $resource = null): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'limit_reached' => true,
                'resource' => $resource,
            ], 403);
        }

        return redirect()->back()
            ->with('error', $message)
            ->with('limit_reached', true);
    }
}
