<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Guest routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Placeholder dashboard routes (will be expanded later)
Route::get('/admin/dashboard', function () {
    if (!auth()->check() || auth()->user()->role !== 'ADMIN') {
        return redirect('/login');
    }
    return view('admin.dashboard');
})->name('admin.dashboard');

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
