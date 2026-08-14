<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\WebsiteAuthController;
use App\Http\Controllers\WebsiteSettingController;
use Illuminate\Support\Facades\Route;

// ------------------------------------------------------------
// WEBSITE PUBLIK — Amanah Nusantara Logistik (tanpa autentikasi)
// Modul terpisah atas permintaan perusahaan (feature/company-website)
// ------------------------------------------------------------
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/tentang', [SiteController::class, 'about'])->name('about');
Route::get('/layanan', [SiteController::class, 'services'])->name('services');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::post('/kontak', [SiteController::class, 'storeContact'])->name('contact.store');
Route::get('/berita', [SiteController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [SiteController::class, 'beritaShow'])->name('berita.show');

// ------------------------------------------------------------
// LOGIN ADMIN WEBSITE (jalur terpisah dari login dashboard).
// Akun yang sama dengan dashboard, hanya role admin yang dibolehkan.
// ------------------------------------------------------------
Route::get('/website/login', [WebsiteAuthController::class, 'create'])->name('website.login');
Route::post('/website/login', [WebsiteAuthController::class, 'store']);

// ------------------------------------------------------------
// CMS WEBSITE (Berita/Artikel) — khusus admin, lewat jalur login website
// ------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('website')->group(function () {
    Route::resource('posts', PostController::class)->except(['show'])->names('website.posts');

    // Pengaturan konten website (logo, layanan, moda, logo klien)
    Route::get('/settings', [WebsiteSettingController::class, 'index'])->name('website.settings.index');
    Route::post('/settings/logo', [WebsiteSettingController::class, 'updateLogo'])->name('website.settings.logo');
    Route::post('/settings/services', [WebsiteSettingController::class, 'updateServices'])->name('website.settings.services');
    Route::post('/settings/clients', [WebsiteSettingController::class, 'storeClient'])->name('website.settings.clients.store');
    Route::delete('/settings/clients/{client}', [WebsiteSettingController::class, 'destroyClient'])->name('website.settings.clients.destroy');
    Route::patch('/settings/clients/{client}/toggle', [WebsiteSettingController::class, 'toggleClient'])->name('website.settings.clients.toggle');
    Route::post('/settings/clients/{client}/move', [WebsiteSettingController::class, 'moveClient'])->name('website.settings.clients.move');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard KPI & Data Pengiriman (semua role terautentikasi)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');

    // Modul Import Data Excel — khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
        Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
        Route::post('/imports/process', [ImportController::class, 'process'])->name('imports.process');
        Route::delete('/imports/clear', [ImportController::class, 'clear'])->name('imports.clear');
    });

    // Modul Laporan & Export — Admin & Project Manager
    Route::middleware('role:admin,project-manager')->group(function () {
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
