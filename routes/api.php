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
use App\Http\Controllers\Api\ProfileAnggotaController;
use App\Http\Controllers\Api\MasterChatReferenceTableController;
use App\Http\Controllers\Api\ChatController;

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

    Route::prefix('/master')->group(function () {
        Route::get('/jenis-pinjaman', [MasterJenisPinjamanController::class, 'getAll']);
        Route::get('/keperluan-pinjaman', [MasterKeperluanPinjamanController::class, 'getAll']);
        Route::get('/status-pengajuan-pinjaman', [MasterStatusPengajuanPinjamanController::class, 'getAll']);
        Route::get('/jenis-tabungan', [MasterJenisTabunganController::class, 'getAll']);
        Route::get('/chat-reference-table', [MasterChatReferenceTableController::class, 'getAll']);
    });

    Route::post('/pinjaman/pengajuan', [PinjamanController::class, 'formPengajuan']);
    Route::post('/pinjaman/list', [PinjamanController::class, 'listPengajuan']);
    Route::get('/pinjaman/preview/{id}', [PinjamanController::class, 'getPengajuanById'])->where('id', '[0-9]+');
    Route::delete('/pinjaman/delete/{id}', [PinjamanController::class, 'deletePengajuanById'])->where('id', '[0-9]+');

    Route::prefix('/tabungan')->group(function () {
        Route::post('/mutasi/list', [TabunganController::class, 'getMutasi']);
        Route::post('/saldo/tahunan', [TabunganController::class, 'getSaldoTahunan']);
        Route::post('/saldo/bulanan', [TabunganController::class, 'getSaldoBulanan']);
        Route::prefix('/pencairan')->group(function () {
            Route::post('/pengajuan', [TabunganController::class, 'formPengajuanPencairan']);
            Route::get('/pengajuan/list', [TabunganController::class, 'listPengajuanPencairan']);
            Route::delete('/pembatalan/{id}', [TabunganController::class, 'batalkanPencairan'])->where('id', '[A-Za-z0-9]+');
        });
    });

    Route::prefix('/tagihan')->group(function () {
        Route::post('/anggota', [TagihanController::class, 'getByAnggota']);
        Route::post('/pinjaman', [TagihanController::class, 'getByNomorPinjaman']);
    });

    Route::prefix('/shu')->group(function () {
        Route::post('', [ShuController::class, 'getByAnggota']);
        Route::post('/grid', [ShuController::class, 'getByAnggotaGrid']);
    });

    Route::prefix('/simulasi')->group(function () {
        Route::post('/pinjaman', [SimulasiPinjamanController::class, 'getSimulasi']);
        Route::post('/tenor', [SimulasiPinjamanController::class, 'getTenorSimulasi']);
    });

    Route::get('/konten', [KontenController::class, 'getAll']);
    Route::post('/konten/grid', [KontenController::class, 'getGrid']);
    Route::get('/konten/{id}', [KontenController::class, 'getById']);
    Route::get('/konten/tipe/{tipe_content}', [KontenController::class, 'getActiveByTipe']);

    Route::get('/profile', [ProfileAnggotaController::class, 'getProfile']);
    Route::post('/profile', [ProfileAnggotaController::class, 'updateProfile']);
    Route::post('/profile/update-photo', [ProfileAnggotaController::class, 'updateProfilePhoto']);

    Route::prefix('/chat')->group(function () {
        Route::post('/ticket/add', [ChatController::class, 'createTicket']);
        Route::get('/ticket/close/{id}', [ChatController::class, 'closeTicket']);
        Route::post('/ticket/grid', [ChatController::class, 'getGrid']);
        Route::post('/message/add', [ChatController::class, 'createMessage']);
        Route::get('/message/open/{chatid}', [ChatController::class, 'openMessage']);
    });
});

Route::post('/anggota/register', [MasterAnggotaController::class, 'register']);
Route::get('/master/unit', [MasterUnitController::class, 'getAll']);

Route::get('/secure-file/{path}', [FileController::class, 'getSecureFile'])
        ->where('path', '.*')
        ->name('secure-file');
