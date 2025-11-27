<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            auth()->user()->updateLastLogin();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|alpha_dash|unique:tenants,subdomain',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $provisioningService = new TenantProvisioningService();
            $tenant = $provisioningService->provision([
                'name' => $validated['name'],
                'company_name' => $validated['company_name'],
                'subdomain' => $validated['subdomain'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'subscription_plan' => 'free',
            ]);

            if (!$tenant) {
                return back()->withErrors([
                    'subdomain' => 'Failed to create tenant. Please try again.',
                ])->withInput();
            }

            $user = User::where('email', $validated['email'])->first();
            
            if ($user) {
                Auth::login($user);
                $user->updateLastLogin();
            }

            return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to ISP Manager.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'subdomain' => 'Registration failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return back()->with('status', 'If an account with that email exists, we have sent a password reset link.');
    }
}
