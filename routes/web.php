<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Page\Dashboard;
use App\Livewire\Page\Master\Anggota\AnggotaList;
use App\Livewire\Page\Tabungan\TabunganList;
use App\Livewire\Page\Tagihan\TagihanList;
use App\Livewire\Page\User\UserList;

Route::get('/', function () {
    // return view('welcome');
});

Route::get('dashboard', Dashboard::class)->name('dashboard');
Route::prefix('user')->group(function () {
    Route::get('list', UserList::class)->name('user.list');
});
Route::prefix('master')->group(function () {
    Route::prefix('anggota')->group(function () {
        Route::get('list', AnggotaList::class)->name('master.anggota.list');
    });
});
Route::prefix('tabungan')->group(function () {
    Route::get('list', TabunganList::class)->name('tabungan.list');
});
Route::prefix('tagihan')->group(function () {
    Route::get('list', TagihanList::class)->name('tagihan.list');
});
