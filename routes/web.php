<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboard;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Admin\PortController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\ContainerController;
use App\Http\Controllers\Operator\ShipmentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/operator/dashboard', [OperatorDashboard::class, 'index'])->name('operator.dashboard');

    Route::get('/customer/dashboard', [CustomerDashboard::class, 'index'])->name('customer.dashboard');

    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/ports/create', [PortController::class, 'create'])->name('ports.create');
    Route::post('/ports', [PortController::class, 'store'])->name('ports.store');
    Route::get('/ports/{id}/edit', [PortController::class, 'edit'])->name('ports.edit');
    Route::put('/ports/{id}', [PortController::class, 'update'])->name('ports.update');
    Route::post('/ports/{id}/toggle', [PortController::class, 'toggleActive'])->name('ports.toggleActive');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{id}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::post('/vehicles/{id}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.updateStatus');

    Route::get('/containers', [ContainerController::class, 'index'])->name('containers.index');
    Route::get('/containers/create', [ContainerController::class, 'create'])->name('containers.create');
    Route::post('/containers', [ContainerController::class, 'store'])->name('containers.store');
    Route::get('/containers/{id}', [ContainerController::class, 'show'])->name('containers.show');
    Route::get('/containers/{id}/edit', [ContainerController::class, 'edit'])->name('containers.edit');
    Route::put('/containers/{id}', [ContainerController::class, 'update'])->name('containers.update');
    Route::post('/containers/{id}/status', [ContainerController::class, 'updateStatus'])->name('containers.updateStatus');

    Route::get('/operator/shipments', [ShipmentController::class, 'index'])->name('operator.shipments.index');
    Route::get('/operator/shipments/create', [ShipmentController::class, 'create'])->name('operator.shipments.create');
    Route::post('/operator/shipments', [ShipmentController::class, 'store'])->name('operator.shipments.store');
    Route::get('/operator/shipments/{id}', [ShipmentController::class, 'show'])->name('operator.shipments.show');
    Route::post('/operator/shipments/{id}/cancel', [ShipmentController::class, 'cancel'])->name('operator.shipments.cancel');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/operator/tracking/log', [TrackingController::class, 'create'])->name('tracking.create');
    Route::get('/tracking/{shipment_ref}', [TrackingController::class, 'show'])->name('tracking.show');
    Route::post('/tracking/log', [TrackingController::class, 'store'])->name('tracking.store');

    // Global Search
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    // Payments Module
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::get('/payments/{id}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/{id}/checkout', [PaymentController::class, 'processCheckout'])->name('payments.processCheckout');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/{id}/toggle', [UserController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    Route::delete('/containers/{id}', [ContainerController::class, 'destroy'])->name('containers.destroy');
});


