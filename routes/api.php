<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RadiusRestController;

Route::post('/radius', [RadiusRestController::class, 'handle'])->name('radius.handle');
Route::get('/radius', [RadiusRestController::class, 'handle'])->name('radius.handle.get');

Route::prefix('radius')->name('radius.')->group(function () {
    Route::post('/authenticate', function (Request $request) {
        $request->merge(['action' => 'authenticate']);
        return app(RadiusRestController::class)->handle($request);
    })->name('authenticate');

    Route::post('/authorize', function (Request $request) {
        $request->merge(['action' => 'authorize']);
        return app(RadiusRestController::class)->handle($request);
    })->name('authorize');

    Route::post('/accounting', function (Request $request) {
        $request->merge(['action' => 'accounting']);
        return app(RadiusRestController::class)->handle($request);
    })->name('accounting');

    Route::post('/post-auth', function (Request $request) {
        $request->merge(['action' => 'post-auth']);
        return app(RadiusRestController::class)->handle($request);
    })->name('post-auth');
});

Route::get('/radius/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Radius REST API is working',
        'endpoints' => [
            'POST /api/radius' => 'Main endpoint with X-FreeRadius-Section header or action parameter',
            'POST /api/radius/authenticate' => 'Authentication endpoint',
            'POST /api/radius/authorize' => 'Authorization endpoint',
            'POST /api/radius/accounting' => 'Accounting endpoint',
            'POST /api/radius/post-auth' => 'Post-authentication logging endpoint',
        ],
        'example_request' => [
            'authenticate' => [
                'username' => 'user123',
                'password' => 'password',
            ],
            'authorize' => [
                'username' => 'user123',
                'password' => 'password',
                'NAS-IP-Address' => '192.168.1.1',
            ],
            'accounting' => [
                'username' => 'user123',
                'Acct-Status-Type' => 'Start',
                'Acct-Session-Id' => 'abc123',
                'NAS-IP-Address' => '192.168.1.1',
            ],
        ],
    ]);
})->name('radius.test');
