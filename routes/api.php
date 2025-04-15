<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\MasterUnitController;
use App\Http\Controllers\Api\MasterAnggotaController;
use App\Http\Controllers\Api\MasterJenisPinjamanController;
use App\Http\Controllers\Api\MasterKeperluanPinjamanController;
use App\Http\Controllers\Api\PinjamanController;
use App\Http\Controllers\Api\TabunganController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\ShuController;

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

    Route::get('/anggota/{p_anggota_id}', [MasterAnggotaController::class, 'getAnggotaById']);
    Route::post('/file/get-link', [FileController::class, 'getLink']);
    Route::get('/master/jenis-pinjaman', [MasterJenisPinjamanController::class, 'getAll']);
    Route::get('/master/keperluan-pinjaman', [MasterKeperluanPinjamanController::class, 'getAll']);
    Route::post('/pinjaman/pengajuan', [PinjamanController::class, 'pengajuan']);
    Route::post('/tabungan', [TabunganController::class, 'getByAnggota']);
    Route::post('/tagihan', [TagihanController::class, 'getByAnggota']);
    Route::post('/shu', [ShuController::class, 'getByAnggota']);
});

Route::post('/anggota/register', [MasterAnggotaController::class, 'register']);
Route::get('/master/unit', [MasterUnitController::class, 'getAll']);

Route::get('/secure-file/{path}', [FileController::class, 'getSecureFile'])
        ->where('path', '.*')
        ->name('secure-file');
