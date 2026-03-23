<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('service-requests', ServiceRequestController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('billing', BillingController::class)->only(['index', 'show', 'destroy']);
    
    // Billing routes for service requests
    Route::get('/service-requests/{serviceRequest}/billing/create', [BillingController::class, 'create'])
        ->name('service-requests.billing.create');
    Route::post('/service-requests/{serviceRequest}/billing/store', [BillingController::class, 'store'])
        ->name('service-requests.billing.store');
    
    // Delete billing for cancelled requests
    Route::delete('/billing/{billing}/delete-cancelled', [BillingController::class, 'deleteForCancelled'])
        ->name('billing.delete-for-cancelled');

    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    Route::post('/queue/process-next', [QueueController::class, 'processNext'])->name('queue.process-next');
    Route::put('/queue/{queue}/position', [QueueController::class, 'updatePosition'])->name('queue.update-position');

    Route::post('/inventory/add-to-service/{serviceId}', [InventoryController::class, 'addToService'])
        ->name('inventory.add-to-service');
    Route::put('/billing/{billing}/payment-status', [BillingController::class, 'updatePaymentStatus'])
        ->name('billing.update-payment-status');

    // Routes for employee management
    Route::resource('employees', EmployeeController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy'
    ]);
});