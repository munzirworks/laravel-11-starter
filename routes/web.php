<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
    : view('landing');
});

Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('landing'))->name('dashboard');
    Route::get('/customers', fn () => view('landing'));
    Route::get('/customers/create', fn () => view('landing'));
    Route::get('/customers/{id}', fn () => view('landing'))->whereNumber('id');
    Route::get('/customers/{id}/edit', fn () => view('landing'))->whereNumber('id');
    Route::get('/products', fn () => view('landing'));
    Route::get('/products/create', fn () => view('landing'));
    Route::get('/products/{id}', fn () => view('landing'))->whereNumber('id');
    Route::get('/products/{id}/edit', fn () => view('landing'))->whereNumber('id');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';
