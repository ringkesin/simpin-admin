<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Page\Dashboard;
use App\Livewire\Page\Master\Anggota\AnggotaList;
use App\Livewire\Page\Master\Anggota\AnggotaCreate;
use App\Livewire\Page\Master\Anggota\AnggotaShow;
use App\Livewire\Page\Master\Anggota\AnggotaEdit;
use App\Livewire\Page\Tabungan\TabunganList;
use App\Livewire\Page\Tagihan\TagihanList;
use App\Livewire\Page\User\UserList;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });
// Route::redirect('/', 'login');

Route::redirect('/', 'login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::prefix('user')->group(function () {
        Route::get('list', UserList::class)->name('user.list');
    });
    Route::prefix('master')->group(function () {
        Route::prefix('anggota')->group(function () {
            Route::get('list', AnggotaList::class)->name('master.anggota.list');
            Route::get('create', AnggotaCreate::class)->name('master.anggota.create');
            Route::get('show/{id}', AnggotaShow::class)->name('master.anggota.show');
            Route::get('edit/{id}', AnggotaEdit::class)->name('master.anggota.edit');
        });
    });
    Route::prefix('tabungan')->group(function () {
        Route::get('list', TabunganList::class)->name('tabungan.list');
    });
    Route::prefix('tagihan')->group(function () {
        Route::get('list', TagihanList::class)->name('tagihan.list');
    });
});
