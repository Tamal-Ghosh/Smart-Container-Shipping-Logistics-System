<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/operator/dashboard', function () {
    if (!auth()->check() || auth()->user()->role !== 'OPERATOR') {
        return redirect('/login');
    }
    return view('operator.dashboard');
})->name('operator.dashboard');

Route::get('/customer/dashboard', function () {
    if (!auth()->check() || auth()->user()->role !== 'CUSTOMER') {
        return redirect('/login');
    }
    return view('customer.dashboard');
})->name('customer.dashboard');
