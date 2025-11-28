<?php

namespace App\Http\Traits;

use App\Services\TenantUsageService;
use Illuminate\Http\JsonResponse;

trait ChecksSubscriptionLimits
{
    protected function checkRouterLimit(): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->canAddRouter($tenant)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimum router. Silakan upgrade paket langganan Anda untuk menambah kapasitas.',
                'limit_reached' => true,
                'resource' => 'router',
            ], 403);
        }

        return null;
    }

    protected function checkCustomerLimit(): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->canAddCustomer($tenant)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimum pelanggan. Silakan upgrade paket langganan Anda untuk menambah kapasitas.',
                'limit_reached' => true,
                'resource' => 'customer',
            ], 403);
        }

        return null;
    }

    protected function checkVoucherLimit(int $count = 1): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->canAddVoucher($tenant, $count)) {
            $remaining = $usageService->getRemainingVouchers($tenant);
            return response()->json([
                'success' => false,
                'message' => "Anda telah mencapai batas voucher. Sisa kapasitas: {$remaining} voucher. Silakan upgrade paket langganan Anda untuk menambah kapasitas.",
                'limit_reached' => true,
                'resource' => 'voucher',
                'remaining' => $remaining,
            ], 403);
        }

        return null;
    }

    protected function checkOnlineUserLimit(): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->canAddOnlineUser($tenant)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimum pengguna online. Silakan upgrade paket langganan Anda untuk menambah kapasitas.',
                'limit_reached' => true,
                'resource' => 'online_user',
            ], 403);
        }

        return null;
    }

    protected function checkFeatureAccess(string $feature): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->hasFeature($tenant, $feature)) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini tidak tersedia dalam paket langganan Anda. Silakan upgrade untuk mengakses fitur ini.',
                'feature_locked' => true,
                'feature' => $feature,
            ], 403);
        }

        return null;
    }

    protected function checkApiAccess(): ?JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 403);
        }

        $usageService = app(TenantUsageService::class);
        
        if (!$usageService->canUseApi($tenant)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses API tidak tersedia dalam paket langganan Anda. Silakan upgrade ke paket Professional atau lebih tinggi.',
                'feature_locked' => true,
                'feature' => 'api_access',
            ], 403);
        }

        return null;
    }

    protected function getUsageStats(): array
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return [];
        }

        $usageService = app(TenantUsageService::class);
        
        return $usageService->getUsageWithLimits($tenant);
    }

    protected function getLimitWarnings(): array
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return [];
        }

        $usageService = app(TenantUsageService::class);
        
        return $usageService->getLimitWarnings($tenant);
    }
}
