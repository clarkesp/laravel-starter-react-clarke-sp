<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Models\AdminActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        return Inertia::render('admin/auth/login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle admin login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('admin')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();

        // Update last login
        $admin->updateLastLogin();

        // Log the login activity
        AdminActivityLog::create([
            'admin_user_id' => $admin->id,
            'action' => 'login',
            'resource_type' => 'auth',
            'description' => 'Admin user logged in',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Log the logout activity
        if ($admin) {
            AdminActivityLog::create([
                'admin_user_id' => $admin->id,
                'action' => 'logout',
                'resource_type' => 'auth',
                'description' => 'Admin user logged out',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
