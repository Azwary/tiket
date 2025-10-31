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
Route::put('/penumpang/{id}', [AuthPenumpangController::class, 'updateProfile']);

//
Route::post('login', [ApiAuthController::class, 'login'])->middleware('guest');

Route::post('/penumpang/register', [AuthPenumpangController::class, 'register']);
// Route::get('/penumpang', [ApiPenumpangController::class, 'index']);
Route::get('/penumpang/{id}', [ApiPenumpangController::class, 'show']);

// Ambil semua rute
Route::get('/rute', [ApiPenumpangController::class, 'getRute']);

// Ambil jam/jadwal berdasarkan rute tertentu
Route::get('/jadwal', [ApiPenumpangController::class, 'getJam']);

// Buat pemesanan baru
Route::post('/pemesanan', [ApiPenumpangController::class, 'store']);
Route::get('tiket/{id_penumpang}', [ApiPenumpangController::class, 'getTiketPenumpang']);


Route::prefix('petugas')->group(function () {
    Route::get('/rute', [ApiPetugasController::class, 'getRute']);
    Route::get('/rute/{id}/jadwal', [ApiPetugasController::class, 'getJam']);
    Route::get('/kursi/tersedia', [ApiPetugasController::class, 'getKursiTersedia']);
    Route::post('/pesan', [ApiPetugasController::class, 'store']);
});
