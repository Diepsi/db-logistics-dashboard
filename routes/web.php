<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard KPI & Data Pengiriman (semua role terautentikasi)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments-search', [ShipmentController::class, 'search'])->name('shipments.search');
    Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->name('shipments.show');

    // Issue Management
    Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
    Route::post('/issues/{issue}/resolve', [IssueController::class, 'resolve'])->name('issues.resolve');
    Route::post('/issues/{issue}/reopen', [IssueController::class, 'reopen'])->name('issues.reopen');

    // Modul Import Data Excel — khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
        Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
        Route::post('/imports/process', [ImportController::class, 'process'])->name('imports.process');
        Route::get('/imports/{batch}/progress', [ImportController::class, 'progress'])->name('imports.progress');
        Route::delete('/imports/clear', [ImportController::class, 'clear'])->name('imports.clear');

        // Manajemen User — khusus Admin
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Modul Analitik, Laporan & Export — Admin, Project Manager & Staff
    Route::middleware('role:admin,project-manager,staff')->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/map-data', [AnalyticsController::class, 'mapData'])->name('analytics.map-data');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    });

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
