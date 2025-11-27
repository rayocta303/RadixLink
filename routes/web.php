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
use App\Http\Controllers\Tenant\NasController;
use App\Http\Controllers\Tenant\ServicePlanController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\VoucherController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\TenantSettingsController;
use App\Http\Controllers\Tenant\RouterScriptController;

Route::get('/', function () {
    return view('welcome');
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

    Route::prefix('platform')->name('platform.')->middleware('platform.admin')->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
        
        Route::resource('subscriptions', SubscriptionController::class);
        Route::resource('invoices', PlatformInvoiceController::class);
        Route::resource('tickets', PlatformTicketController::class);
        Route::post('tickets/{ticket}/reply', [PlatformTicketController::class, 'reply'])->name('tickets.reply');
        
        Route::resource('users', PlatformUserController::class);
        Route::get('settings', [PlatformSettingsController::class, 'index'])->name('settings');
        Route::put('settings', [PlatformSettingsController::class, 'update'])->name('settings.update');
    });

    Route::prefix('tenant')->name('tenant.')->middleware('tenant.user')->group(function () {
        Route::resource('nas', NasController::class);
        Route::post('nas/{nas}/test', [NasController::class, 'test'])->name('nas.test');
        Route::get('nas-map', [NasController::class, 'map'])->name('nas.map');
        
        Route::resource('services', ServicePlanController::class);
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/suspend', [CustomerController::class, 'suspend'])->name('customers.suspend');
        Route::post('customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
        
        Route::resource('vouchers', VoucherController::class);
        Route::get('vouchers/generate', [VoucherController::class, 'showGenerate'])->name('vouchers.generate');
        Route::post('vouchers/generate', [VoucherController::class, 'generate'])->name('vouchers.generate.store');
        Route::get('vouchers/print/{batch}', [VoucherController::class, 'print'])->name('vouchers.print');
        
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        
        Route::get('settings', [TenantSettingsController::class, 'index'])->name('settings');
        Route::put('settings', [TenantSettingsController::class, 'update'])->name('settings.update');

        Route::prefix('router-scripts')->name('router-scripts.')->group(function () {
            Route::get('/', [RouterScriptController::class, 'index'])->name('index');
            Route::post('/generate', [RouterScriptController::class, 'generate'])->name('generate');
            Route::post('/generate-customer', [RouterScriptController::class, 'generateCustomerScript'])->name('generate-customer');
            Route::post('/generate-nas-client', [RouterScriptController::class, 'generateNasClient'])->name('generate-nas-client');
            Route::post('/download', [RouterScriptController::class, 'download'])->name('download');
        });
    });
});
