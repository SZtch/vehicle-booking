<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Bookings
        Route::resource('bookings', BookingController::class)->except(['edit', 'update']);

        // Vehicles
        Route::resource('vehicles', VehicleController::class);

        // Drivers
        Route::resource('drivers', DriverController::class);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Approver only routes
    Route::middleware(['role:approver'])->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{approval}/process', [ApprovalController::class, 'process'])->name('approvals.process');
    });
});

require __DIR__.'/auth.php';
