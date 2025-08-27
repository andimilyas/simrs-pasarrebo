<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ResepController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('pasien', PasienController::class);
    Route::get('pasien/{pasien}/download-dokumen', [PasienController::class, 'downloadDokumen'])
        ->name('pasien.download-dokumen');
});

// Admin routes with role middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    
    // Pasien management routes
    Route::resource('pasien', PasienController::class);
    Route::get('/pasien/search', [PasienController::class, 'search'])->name('pasien.search');
    Route::get('/pasien/export/pdf', [PasienController::class, 'exportPdf'])->name('pasien.export.pdf');
    Route::get('/pasien/export/excel', [PasienController::class, 'exportExcel'])->name('pasien.export.excel');
    
    // Pendaftaran management routes
    Route::resource('pendaftaran', PendaftaranController::class);
    Route::patch('/pendaftaran/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.update-status');
    Route::get('/pendaftaran/antrian', [PendaftaranController::class, 'antrian'])->name('pendaftaran.antrian');
    
    // Obat management routes (Admin only)
    Route::resource('obat', ObatController::class);
    Route::patch('/obat/{obat}/stok', [ObatController::class, 'updateStok'])->name('obat.update-stok');
    Route::get('/obat/search', [ObatController::class, 'search'])->name('obat.search');
    
    // Resep management routes
    Route::resource('resep', ResepController::class);
    Route::patch('/resep/{resep}/status', [ResepController::class, 'updateStatus'])->name('resep.update-status');
    Route::get('/resep/{resep}/print', [ResepController::class, 'print'])->name('resep.print');
    Route::get('/resep/obat/kategori', [ResepController::class, 'getObatByKategori'])->name('resep.obat-by-kategori');
});

require __DIR__.'/auth.php';
