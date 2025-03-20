<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\AnggotaController;

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

    Route::get('/anggota/{p_anggota_id}', [AnggotaController::class, 'getAnggotaById']);

    Route::post('/file/get-link', [FileController::class, 'getLink']);
});

Route::post('/anggota/register', [AnggotaController::class, 'register']);
Route::get('/unit', [UnitController::class, 'getAll']);

Route::get('/secure-file/{path}', [FileController::class, 'getSecureFile'])
        ->where('path', '.*')
        ->name('secure-file');