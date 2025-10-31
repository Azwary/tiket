<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PenumpangLoginController;
use App\Http\Controllers\Auth\PetugasLoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\SupirsController;
use App\Http\Controllers\userscontroller;
use Illuminate\Support\Facades\Route;


// routes/web.php
Route::get('/', fn() => redirect('/login'));

// Form reset password
Route::get('/admin/password/request', [AdminAuthController::class, 'showResetForm'])
    ->name('admin.password.request');

Route::post('/admin/password/request', [AdminAuthController::class, 'handleResetRequest'])
    ->name('admin.password.request.post'); // <-- pastikan ini


// Reset password setelah validasi tanggal lahir
Route::get('/admin/password/reset/{username}', [AdminAuthController::class, 'showNewPasswordForm'])
    ->name('admin.password.reset'); // <-- ini route custom

Route::post('/admin/password/reset/{username}', [AdminAuthController::class, 'updatePassword'])
    ->name('admin.password.update');

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
    Route::resource('supir', SupirsController::class);
    // Jadwal
    Route::get('jadwal', [SupirsController::class, 'jadwalIndex'])->name('supir.jadwal');
    Route::post('jadwal', [SupirsController::class, 'jadwalStore'])->name('supir.jadwal.store');
    Route::get('jadwal/{id}/edit', [SupirsController::class, 'jadwalEdit'])->name('supir.jadwal.edit');
    Route::put('jadwal/{id}', [SupirsController::class, 'jadwalUpdate'])->name('supir.jadwal.update');
    Route::delete('jadwal/{id}', [SupirsController::class, 'jadwalDestroy'])->name('supir.jadwal.destroy');


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


require __DIR__ . '/auth.php';
