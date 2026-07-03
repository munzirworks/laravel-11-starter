<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
    : view('landing');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';

// React SPA routes (client-side auth via Sanctum tokens, module 12B).
// Registered after auth.php: Laravel's route collection is keyed by
// method+URI, so the last route registered for a given method+URI wins
// the actual request match — these intercept GET /login and /register
// ahead of Breeze's session-based views. The 'login'/'register' route
// *names* still resolve to Breeze's routes (registered first) for any
// internal redirect()->route('login') calls, which is fine since the
// resulting redirect to /login is handled by this same React shell.
// Breeze's controllers remain fully intact and reachable; the React app
// simply talks to the API (/api/login, /api/register) instead of posting
// to these session-based routes.
Route::get('/login', fn () => view('landing'));
Route::get('/register', fn () => view('landing'));
Route::get('/app/{any?}', fn () => view('landing'))->where('any', '.*');
