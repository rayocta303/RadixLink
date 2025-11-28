<?php

namespace App\Http\Middleware;

use App\Services\TenantUsageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    public function __construct(
        protected TenantUsageService $usageService
    ) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return $this->featureLockedResponse('Tenant tidak ditemukan.');
        }

        $hasAccess = match ($feature) {
            'api' => $this->usageService->canUseApi($tenant),
            'custom_domain' => $this->usageService->canUseCustomDomain($tenant),
            'priority_support' => $this->usageService->hasPrioritySupport($tenant),
            'hotspot' => $this->usageService->hasFeature($tenant, 'hotspot'),
            'pppoe' => $this->usageService->hasFeature($tenant, 'pppoe'),
            'sms_notification' => $this->usageService->hasFeature($tenant, 'sms_notification'),
            'email_notification' => $this->usageService->hasFeature($tenant, 'email_notification'),
            'advanced_reports' => $this->usageService->hasFeature($tenant, 'advanced_reports'),
            'voucher_templates' => $this->usageService->hasFeature($tenant, 'voucher_templates'),
            'multi_location' => $this->usageService->hasFeature($tenant, 'multi_location'),
            'reseller_module' => $this->usageService->hasFeature($tenant, 'reseller_module'),
            'advanced_analytics' => $this->usageService->hasFeature($tenant, 'advanced_analytics'),
            'white_label' => $this->usageService->hasFeature($tenant, 'white_label'),
            default => $this->usageService->hasFeature($tenant, $feature),
        };

        if (!$hasAccess) {
            $message = $this->getFeatureMessage($feature);
            return $this->featureLockedResponse($message, $feature);
        }

        return $next($request);
    }

    protected function getFeatureMessage(string $feature): string
    {
        return match ($feature) {
            'api' => 'Akses API tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'custom_domain' => 'Custom domain tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'priority_support' => 'Priority support tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'hotspot' => 'Fitur Hotspot tidak tersedia dalam paket Anda. Upgrade ke paket Starter atau lebih tinggi.',
            'pppoe' => 'Fitur PPPoE tidak tersedia dalam paket Anda. Upgrade ke paket Starter atau lebih tinggi.',
            'sms_notification' => 'Notifikasi SMS tidak tersedia dalam paket Anda. Upgrade ke paket Basic atau lebih tinggi.',
            'email_notification' => 'Notifikasi Email tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'advanced_reports' => 'Laporan Lanjutan tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'voucher_templates' => 'Template Voucher tidak tersedia dalam paket Anda. Upgrade ke paket Professional atau lebih tinggi.',
            'multi_location' => 'Multi Lokasi tidak tersedia dalam paket Anda. Upgrade ke paket Business atau lebih tinggi.',
            'reseller_module' => 'Modul Reseller tidak tersedia dalam paket Anda. Upgrade ke paket Business atau lebih tinggi.',
            'advanced_analytics' => 'Analitik Lanjutan tidak tersedia dalam paket Anda. Upgrade ke paket Business atau lebih tinggi.',
            'white_label' => 'White Label tidak tersedia dalam paket Anda. Upgrade ke paket Enterprise atau lebih tinggi.',
            default => "Fitur '{$feature}' tidak tersedia dalam paket langganan Anda.",
        };
    }

    protected function featureLockedResponse(string $message, ?string $feature = null): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'feature_locked' => true,
                'feature' => $feature,
            ], 403);
        }

        return redirect()->back()
            ->with('error', $message)
            ->with('feature_locked', true);
    }
}
