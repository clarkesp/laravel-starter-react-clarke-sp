<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Models\AdminActivityLog;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Get statistics
        $stats = [
            'total_admins' => AdminUser::count(),
            'active_admins' => AdminUser::active()->count(),
            'total_roles' => AdminRole::count(),
            'total_permissions' => AdminPermission::count(),
        ];

        // Get recent activity (last 10)
        $recentActivity = AdminActivityLog::with('adminUser')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->adminUser ? $log->adminUser->name : 'Unknown',
                    'action' => $log->action,
                    'resource_type' => $log->resource_type,
                    'description' => $log->description,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        // Get admin's roles and permissions
        $adminData = [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'roles' => $admin->roles->pluck('name'),
            'is_super_admin' => $admin->isSuperAdmin(),
        ];

        return Inertia::render('admin/dashboard', [
            'admin' => $adminData,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
