<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PenumpangLoginController;
use App\Http\Controllers\Auth\PetugasLoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\userscontroller;
use Illuminate\Support\Facades\Route;


// routes/web.php
Route::get('/', fn() => redirect('/login'));

// Admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('login.admin');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('logout.admin');
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('pemesanan', PemesananController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('laporan', LaporanController::class)->except(['show']);
    Route::resource('users', userscontroller::class);
    Route::get('/get-jadwal-by-rute/{id_rute}', [PemesananController::class, 'getByRute']);

    Route::get('/get-jadwal', [PemesananController::class, 'getJadwal'])->name('get.jadwal');
    Route::get('/admin/get-kursi', [PemesananController::class, 'getKursi']);
    Route::get('/get-jam-keberangkatan/{id_rute}', [PemesananController::class, 'getJamKeberangkatan']);
    Route::get('/penumpang/show-kursi', [PemesananController::class, 'showKursi'])->name('penumpang.showKursi');
    Route::get('/show-kursi', [PemesananController::class, 'showKursi']);
    Route::get('/tampilkan-kursi', [PemesananController::class, 'tampilkanKursi'])->name('penumpang.tampilkanKursi');
    Route::post('/pembayaran/{id}/konfirmasi', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    Route::get('/laporan/unduh', [LaporanController::class, 'unduh'])->name('laporan.unduh');
    
});



// Petugas
Route::get('/petugas/login', [PetugasLoginController::class, 'showLoginForm']);
Route::post('/petugas/login', [PetugasLoginController::class, 'login']);
Route::post('/petugas/logout', [PetugasLoginController::class, 'logout']);
Route::get('/petugas/dashboard', function () {
    return 'Halaman Dashboard Petugas';
})->middleware('auth:petugas');

// Penumpang
Route::get('/penumpang/login', [PenumpangLoginController::class, 'showLoginForm']);
Route::post('/penumpang/login', [PenumpangLoginController::class, 'login']);
Route::post('/penumpang/logout', [PenumpangLoginController::class, 'logout']);
Route::get('/penumpang/dashboard', function () {
    return 'Halaman Dashboard Penumpang';
})->middleware('auth:penumpang');

// ✅ Tetap aktifkan ini agar route bawaan Breeze tetap bekerja
require __DIR__ . '/auth.php';
