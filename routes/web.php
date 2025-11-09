<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminSessionController;

/*
|--------------------------------------------------------------------------
| Public Frontend (Users)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
| Guard: admin
| Views:
| - resources/js/pages/admin/welcome.tsx
| - resources/js/pages/admin/auth/admin-login.tsx
| - resources/js/pages/admin/dashboard.tsx
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Admin Area (separate guard, new components)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->as('admin.')
    ->group(function () {
        // GET /admin -> admin welcome
        Route::get('/', function () {
            return Inertia::render('app-admin/app-admin-welcome');
        })->name('welcome');

        // Guest-only admin auth
        Route::middleware('guest:admin')->group(function () {
            // GET /admin/login -> admin login page
            Route::get('/login', function () {
                return Inertia::render('app-admin/app-admin-auth/app-admin-login', [
                    'canResetPassword' => true,
                    'canRegister' => false,
                ]);
            })->name('login');

            // POST /admin/login -> uses AdminSessionController
            Route::post('/login', [AdminSessionController::class, 'store'])
                ->name('login.store');
        });

        // Authenticated admin area
        Route::middleware('auth:admin')->group(function () {
            // GET /admin/dashboard
            Route::get('/dashboard', function () {
                return Inertia::render('app-admin/app-admin-dashboard');
            })->name('dashboard');

            // POST /admin/logout
            Route::post('/logout', [AdminSessionController::class, 'destroy'])
                ->name('logout');
        });
    });

require __DIR__.'/settings.php';
