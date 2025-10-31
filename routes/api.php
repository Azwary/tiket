<?php

use App\Http\Controllers\API\AuthPenumpangController;
use App\Http\Controllers\API\ApiAuthController;
use App\Http\Controllers\API\ApiPenumpangController;
use App\Http\Controllers\API\ApiPetugasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::options('/{any}', function (Request $request) {
    return response()->noContent(204);
})->where('any', '.*');


// Login penumpang
Route::post('/penumpang/register', [AuthPenumpangController::class, 'register']);
Route::post('/penumpang/login', [AuthPenumpangController::class, 'login']);
Route::post('/penumpang/logout', [AuthPenumpangController::class, 'logout']);
// Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('login', [ApiAuthController::class, 'login'])->middleware('guest');


// Login Petugas
// Route::post('/petugas/login', [AuthPetugasController::class, 'login']);
// Route::post('/petugas/logout', [AuthPetugasController::class, 'logout']);
Route::post('/penumpang/register', [AuthPenumpangController::class, 'register']);

// Route::get('/penumpang', [ApiPenumpangController::class, 'index']);
Route::get('/penumpang/{id}', [ApiPenumpangController::class, 'show']);

Route::get('/rute', [ApiPenumpangController::class, 'rute']);
Route::get('/rute/{id}/jadwal', [ApiPenumpangController::class, 'getJam']);
Route::get('/kursi/tersedia', [ApiPenumpangController::class, 'showKursi']);
Route::get('/pemesanan/terakhir', [ApiPenumpangController::class, 'pesananterkhir']);
Route::get('/detailpemesanan/terakhir', [ApiPenumpangController::class, 'detailpesananterkhir']);
Route::get('/jadwal', [ApiPenumpangController::class, 'getJadwal']);
Route::post('/upload-bukti', [ApiPenumpangController::class, 'uploadBukti']);
Route::post('/pesan', [ApiPenumpangController::class, 'store']);

Route::post('/petugas/pesan', [ApiPetugasController::class, 'store']);
// Route::get('/rute', [ApiPetugasController::class, 'rute']);
// Route::get('/rute/{id}/jadwal', [ApiPetugasController::class, 'getJam']);
// Route::get('/kursi/tersedia', [ApiPetugasController::class, 'showKursi']);
// Route::get('/pemesanan/terakhir', [ApiPetugasController::class, 'pesananterkhir']);
// Route::get('/detailpemesanan/terakhir', [ApiPetugasController::class, 'detailpesananterkhir']);
// Route::get('/jadwal', [ApiPetugasController::class, 'getJadwal']);
