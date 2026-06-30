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

use App\Http\Controllers\Operator\DashboardController as OperatorDashboard;

Route::get('/operator/dashboard', [OperatorDashboard::class, 'index'])->name('operator.dashboard');

use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;

Route::get('/customer/dashboard', [CustomerDashboard::class, 'index'])->name('customer.dashboard');

use App\Http\Controllers\Admin\PortController;

Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
Route::get('/ports/create', [PortController::class, 'create'])->name('ports.create');
Route::post('/ports', [PortController::class, 'store'])->name('ports.store');
Route::get('/ports/{id}/edit', [PortController::class, 'edit'])->name('ports.edit');
Route::put('/ports/{id}', [PortController::class, 'update'])->name('ports.update');
Route::post('/ports/{id}/toggle', [PortController::class, 'toggleActive'])->name('ports.toggleActive');
