<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Platform\TenantController;
use App\Http\Controllers\Platform\SubscriptionController;
use App\Http\Controllers\Platform\PlatformInvoiceController;
use App\Http\Controllers\Platform\PlatformTicketController;
use App\Http\Controllers\Platform\PlatformUserController;
use App\Http\Controllers\Platform\PlatformSettingsController;
use App\Http\Controllers\Platform\RoleController;
use App\Http\Controllers\Platform\MonitoringController;
use App\Http\Controllers\Platform\ActivityLogController;
use App\Http\Controllers\Tenant\NasController;
use App\Http\Controllers\Tenant\ServicePlanController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\VoucherController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\TenantSettingsController;
use App\Http\Controllers\Tenant\RouterScriptController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\RoleController as TenantRoleController;
use App\Http\Controllers\Tenant\MonitoringController as TenantMonitoringController;
use App\Http\Controllers\Tenant\ActivityLogController as TenantActivityLogController;
use App\Http\Controllers\Tenant\VoucherTemplateController;
use App\Http\Controllers\Tenant\IpPoolController;
use App\Http\Controllers\Tenant\BandwidthController;
use App\Http\Controllers\Tenant\PppoeController;
use App\Http\Controllers\Tenant\HotspotController;

Route::get('/', function () {
    $plans = \App\Models\SubscriptionPlan::active()->ordered()->get();
    return view('welcome', compact('plans'));
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('platform')->name('platform.')->middleware('platform.role:super_admin,platform_admin,platform_cashier,platform_technician,platform_support')->group(function () {
        Route::middleware('platform.role:super_admin,platform_admin')->group(function () {
            Route::resource('tenants', TenantController::class);
            Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
            Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');

            Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring');
            Route::get('monitoring/stats', [MonitoringController::class, 'stats'])->name('monitoring.stats');
        });

        Route::middleware('platform.role:super_admin,platform_admin,platform_cashier')->group(function () {
            Route::resource('subscriptions', SubscriptionController::class);
            Route::resource('invoices', PlatformInvoiceController::class);
        });

        Route::middleware('platform.role:super_admin,platform_admin,platform_support')->group(function () {
            Route::resource('tickets', PlatformTicketController::class);
            Route::post('tickets/{ticket}/reply', [PlatformTicketController::class, 'reply'])->name('tickets.reply');
        });

        Route::middleware('platform.role:super_admin,platform_admin')->group(function () {
            Route::resource('users', PlatformUserController::class);
            Route::get('settings', [PlatformSettingsController::class, 'index'])->name('settings');
            Route::put('settings', [PlatformSettingsController::class, 'update'])->name('settings.update');
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        Route::middleware('platform.role:super_admin')->group(function () {
            Route::resource('roles', RoleController::class);
        });
    });

    Route::prefix('tenant')->name('tenant.')->middleware('tenant.user')->group(function () {
        Route::middleware('tenant.role:owner,admin,technician')->group(function () {
            Route::resource('nas', NasController::class);
            Route::post('nas/{nas}/test', [NasController::class, 'test'])->name('nas.test');
            Route::get('nas-map', [NasController::class, 'map'])->name('nas.map');

            Route::resource('ip-pools', IpPoolController::class);

            Route::resource('bandwidth', BandwidthController::class);

            Route::prefix('pppoe')->name('pppoe.')->group(function () {
                Route::get('/', [PppoeController::class, 'index'])->name('index');
                Route::get('/profiles/create', [PppoeController::class, 'createProfile'])->name('profiles.create');
                Route::post('/profiles', [PppoeController::class, 'storeProfile'])->name('profiles.store');
                Route::get('/profiles/{id}/edit', [PppoeController::class, 'editProfile'])->name('profiles.edit');
                Route::put('/profiles/{id}', [PppoeController::class, 'updateProfile'])->name('profiles.update');
                Route::delete('/profiles/{id}', [PppoeController::class, 'destroyProfile'])->name('profiles.destroy');
                Route::post('/profiles/{id}/script', [PppoeController::class, 'generateScript'])->name('profiles.script');
                Route::get('/servers/create', [PppoeController::class, 'createServer'])->name('servers.create');
                Route::post('/servers', [PppoeController::class, 'storeServer'])->name('servers.store');
                Route::get('/servers/{id}/edit', [PppoeController::class, 'editServer'])->name('servers.edit');
                Route::put('/servers/{id}', [PppoeController::class, 'updateServer'])->name('servers.update');
                Route::delete('/servers/{id}', [PppoeController::class, 'destroyServer'])->name('servers.destroy');
            });

            Route::prefix('hotspot')->name('hotspot.')->group(function () {
                Route::get('/', [HotspotController::class, 'index'])->name('index');
                Route::get('/profiles/create', [HotspotController::class, 'createProfile'])->name('profiles.create');
                Route::post('/profiles', [HotspotController::class, 'storeProfile'])->name('profiles.store');
                Route::get('/profiles/{id}/edit', [HotspotController::class, 'editProfile'])->name('profiles.edit');
                Route::put('/profiles/{id}', [HotspotController::class, 'updateProfile'])->name('profiles.update');
                Route::delete('/profiles/{id}', [HotspotController::class, 'destroyProfile'])->name('profiles.destroy');
                Route::post('/profiles/{id}/script', [HotspotController::class, 'generateScript'])->name('profiles.script');
                Route::get('/servers/create', [HotspotController::class, 'createServer'])->name('servers.create');
                Route::post('/servers', [HotspotController::class, 'storeServer'])->name('servers.store');
                Route::get('/servers/{id}/edit', [HotspotController::class, 'editServer'])->name('servers.edit');
                Route::put('/servers/{id}', [HotspotController::class, 'updateServer'])->name('servers.update');
                Route::delete('/servers/{id}', [HotspotController::class, 'destroyServer'])->name('servers.destroy');
            });

            Route::get('monitoring', [TenantMonitoringController::class, 'index'])->name('monitoring');
            Route::get('monitoring/stats', [TenantMonitoringController::class, 'stats'])->name('monitoring.stats');
            Route::get('monitoring/online', [TenantMonitoringController::class, 'onlineUsers'])->name('monitoring.online');
        });

        Route::middleware('tenant.role:owner,admin')->group(function () {
            Route::resource('services', ServicePlanController::class);
        });

        Route::middleware('tenant.role:owner,admin,reseller')->group(function () {
            Route::resource('customers', CustomerController::class);
            Route::post('customers/{customer}/suspend', [CustomerController::class, 'suspend'])->name('customers.suspend');
            Route::post('customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
        });

        Route::middleware('tenant.role:owner,admin,cashier,reseller')->group(function () {
            // Custom voucher routes BEFORE resource to prevent route conflicts
            Route::get('vouchers/generate', [VoucherController::class, 'showGenerate'])->name('vouchers.generate');
            Route::post('vouchers/generate', [VoucherController::class, 'generate'])->name('vouchers.generate.store');
            Route::get('vouchers/print-selected', [VoucherController::class, 'printSelected'])->name('vouchers.print-selected');
            Route::get('vouchers/print/{batch}', [VoucherController::class, 'print'])->name('vouchers.print');
            Route::post('vouchers/bulk-delete', [VoucherController::class, 'bulkDelete'])->name('vouchers.bulk-delete');
            Route::get('vouchers/batches', [VoucherController::class, 'batches'])->name('vouchers.batches');
            Route::resource('vouchers', VoucherController::class);
        });

        Route::middleware('tenant.role:owner,admin')->group(function () {
            Route::resource('voucher-templates', VoucherTemplateController::class);
            Route::post('voucher-templates/{voucher_template}/set-default', [VoucherTemplateController::class, 'setDefault'])->name('voucher-templates.set-default');
            Route::post('voucher-templates/{voucher_template}/duplicate', [VoucherTemplateController::class, 'duplicate'])->name('voucher-templates.duplicate');
            Route::post('voucher-templates/preview', [VoucherTemplateController::class, 'preview'])->name('voucher-templates.preview');
        });

        Route::middleware('tenant.role:owner,admin,cashier')->group(function () {
            Route::resource('invoices', InvoiceController::class);
            Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
            Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        });

        Route::middleware('tenant.role:owner,admin,cashier,investor')->group(function () {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
            Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        });

        Route::middleware('tenant.role:owner,admin')->group(function () {
            Route::get('settings', [TenantSettingsController::class, 'index'])->name('settings');
            Route::put('settings', [TenantSettingsController::class, 'update'])->name('settings.update');
            Route::resource('users', UserController::class);
            Route::resource('roles', TenantRoleController::class);
            Route::get('activity-logs', [TenantActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        Route::middleware('tenant.role:owner,admin,technician')->prefix('router-scripts')->name('router-scripts.')->group(function () {
            Route::get('/', [RouterScriptController::class, 'index'])->name('index');
            Route::post('/generate', [RouterScriptController::class, 'generate'])->name('generate');
            Route::post('/generate-customer', [RouterScriptController::class, 'generateCustomerScript'])->name('generate-customer');
            Route::post('/generate-nas-client', [RouterScriptController::class, 'generateNasClient'])->name('generate-nas-client');
            Route::post('/download', [RouterScriptController::class, 'download'])->name('download');
        });
    });
});