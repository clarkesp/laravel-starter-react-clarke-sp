<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the routes for the admin panel. These routes are isolated
| from the main application routes and use the 'admin' guard for
| authentication. All routes are prefixed with '/admin'.
|
*/

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware(['admin.auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');

    // Admin Users Management
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    // Roles Management
    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'show' => 'admin.roles.show',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);

    // Permissions Management
    Route::resource('permissions', PermissionController::class)->names([
        'index' => 'admin.permissions.index',
        'create' => 'admin.permissions.create',
        'store' => 'admin.permissions.store',
        'show' => 'admin.permissions.show',
        'edit' => 'admin.permissions.edit',
        'update' => 'admin.permissions.update',
        'destroy' => 'admin.permissions.destroy',
    ]);

    // Generic Resource Management (for future resources)
    Route::prefix('resources')->name('admin.resources.')->group(function () {
        Route::get('/{resource}', [ResourceController::class, 'index'])->name('index');
        Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('create');
        Route::post('/{resource}', [ResourceController::class, 'store'])->name('store');
        Route::get('/{resource}/{id}', [ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('edit');
        Route::put('/{resource}/{id}', [ResourceController::class, 'update'])->name('update');
        Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->name('destroy');
    });
});
