<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\MasterUnitController;
use App\Http\Controllers\Api\MasterAnggotaController;
use App\Http\Controllers\Api\MasterJenisPinjamanController;
use App\Http\Controllers\Api\MasterKeperluanPinjamanController;
use App\Http\Controllers\Api\MasterStatusPengajuanPinjamanController;
use App\Http\Controllers\Api\MasterJenisTabunganController;
use App\Http\Controllers\Api\PinjamanController;
use App\Http\Controllers\Api\TabunganController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\ShuController;
use App\Http\Controllers\Api\SimulasiPinjamanController;
use App\Http\Controllers\Api\KontenController;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    });

    Route::get('/anggota/{p_anggota_id}', [MasterAnggotaController::class, 'getAnggotaById'])->where('id', '[0-9]+');
    Route::post('/file/get-link', [FileController::class, 'getLink']);
    Route::get('/master/jenis-pinjaman', [MasterJenisPinjamanController::class, 'getAll']);
    Route::get('/master/keperluan-pinjaman', [MasterKeperluanPinjamanController::class, 'getAll']);
    Route::get('/master/status-pengajuan-pinjaman', [MasterStatusPengajuanPinjamanController::class, 'getAll']);
    Route::get('/master/jenis-tabungan', [MasterJenisTabunganController::class, 'getAll']);
    Route::post('/pinjaman/pengajuan', [PinjamanController::class, 'formPengajuan']);
    Route::post('/pinjaman/list', [PinjamanController::class, 'listPengajuan']);
    Route::get('/pinjaman/preview/{id}', [PinjamanController::class, 'getPengajuanById'])->where('id', '[0-9]+');
    Route::delete('/pinjaman/delete/{id}', [PinjamanController::class, 'deletePengajuanById'])->where('id', '[0-9]+');
    // Route::post('/tabungan', [TabunganController::class, 'getByAnggota']);
    Route::post('/tabungan/saldo/tahunan', [TabunganController::class, 'getSaldoTahunan']);
    Route::post('/tabungan/saldo/bulanan', [TabunganController::class, 'getSaldoBulanan']);
    Route::post('/tagihan', [TagihanController::class, 'getByAnggota']);
    Route::prefix('/shu')->group(function () {
        Route::post('', [ShuController::class, 'getByAnggota']);
        Route::post('/grid', [ShuController::class, 'getByAnggotaGrid']);
    });
    Route::prefix('/simulasi')->group(function () {
        Route::post('/pinjaman', [SimulasiPinjamanController::class, 'getSimulasi']);
        Route::post('/tenor', [SimulasiPinjamanController::class, 'getTenorSimulasi']);
    });
    Route::get('/konten', [KontenController::class, 'getAll']);
    Route::get('/konten/{id}', [KontenController::class, 'getById']);
    Route::get('/konten/tipe/{tipe_content}', [KontenController::class, 'getActiveByTipe']);
});

Route::post('/anggota/register', [MasterAnggotaController::class, 'register']);
Route::get('/master/unit', [MasterUnitController::class, 'getAll']);

Route::get('/secure-file/{path}', [FileController::class, 'getSecureFile'])
        ->where('path', '.*')
        ->name('secure-file');
