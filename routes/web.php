<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Page\Dashboard;
use App\Livewire\Page\Master\Anggota\AnggotaList;
use App\Livewire\Page\Master\Anggota\AnggotaCreate;
use App\Livewire\Page\Master\Anggota\AnggotaShow;
use App\Livewire\Page\Master\Anggota\AnggotaEdit;
use App\Livewire\Page\Main\Tabungan\TabunganList;
use App\Livewire\Page\Main\Tabungan\TabunganCreate;
use App\Livewire\Page\Main\Tabungan\TabunganShow;
use App\Livewire\Page\Main\Tabungan\TabunganEdit;
use App\Livewire\Page\Main\Tabungan\TabunganExport;
use App\Livewire\Page\Main\Tagihan\TagihanList;
use App\Livewire\Page\Main\Tagihan\TagihanCreate;
use App\Livewire\Page\Main\Tagihan\TagihanShow;
use App\Livewire\Page\Main\Tagihan\TagihanEdit;
use App\Livewire\Page\Main\Tagihan\TagihanExport;
use App\Livewire\Page\Main\Shu\ShuList;
use App\Livewire\Page\Main\Shu\ShuCreate;
use App\Livewire\Page\Main\Shu\ShuShow;
use App\Livewire\Page\Main\Shu\ShuEdit;
use App\Livewire\Page\Main\Shu\ShuExport;
use App\Livewire\Page\User\UserList;
use App\Livewire\Page\User\UserEdit;
use App\Livewire\Page\User\UserCreate;
use App\Livewire\Page\User\UserShow;
use App\Livewire\Page\Master\Simulasi\SimulasiList;
use App\Livewire\Page\Master\Simulasi\SimulasiShow;
//use App\Livewire\Page\Master\Anggota\AnggotaShow;
//use App\Livewire\Page\Master\Anggota\AnggotaEdit;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });
// Route::redirect('/', 'login');

Route::redirect('/', 'login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'authenticate'
])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::prefix('user')->group(function () {
        Route::get('list', UserList::class)->name('user.list');
        Route::get('create', UserCreate::class)->name('user.create');
        Route::get('show/{id}', UserShow::class)->name('user.show');
        Route::get('edit/{id}', UserEdit::class)->name('user.edit');
    });
    Route::prefix('master')->group(function () {
        Route::prefix('anggota')->group(function () {
            Route::get('list', AnggotaList::class)->name('master.anggota.list');
            Route::get('create', AnggotaCreate::class)->name('master.anggota.create');
            Route::get('show/{id}', AnggotaShow::class)->name('master.anggota.show');
            Route::get('edit/{id}', AnggotaEdit::class)->name('master.anggota.edit');
        });

        Route::prefix('simulasi')->group(function () {
            Route::get('list', SimulasiList::class)->name('master.simulasi.list');
        //    Route::get('create', SimulasiCreate::class)->name('master.simulasi.create');
            Route::get('show/{id}', SimulasiShow::class)->name('master.simulasi.show');
        //    Route::get('edit/{id}', SimulasiEdit::class)->name('master.simulasi.edit');
        });
    });
    Route::prefix('main')->group(function () {
        Route::prefix('tabungan')->group(function () {
            Route::get('list', TabunganList::class)->name('main.tabungan.list');
            Route::get('create', TabunganCreate::class)->name('main.tabungan.create');
            Route::get('export', TabunganExport::class)->name('main.tabungan.export');
            Route::get('show/{id}', TabunganShow::class)->name('main.tabungan.show');
            Route::get('edit/{id}', TabunganEdit::class)->name('main.tabungan.edit');
        });
        Route::prefix('tagihan')->group(function () {
            Route::get('list', TagihanList::class)->name('main.tagihan.list');
            Route::get('create', TagihanCreate::class)->name('main.tagihan.create');
            Route::get('export', TagihanExport::class)->name('main.tagihan.export');
            Route::get('show/{id}', TagihanShow::class)->name('main.tagihan.show');
            Route::get('edit/{id}', TagihanEdit::class)->name('main.tagihan.edit');
        });
        Route::prefix('shu')->group(function () {
            Route::get('list', ShuList::class)->name('main.shu.list');
            Route::get('create', ShuCreate::class)->name('main.shu.create');
            Route::get('export', ShuExport::class)->name('main.shu.export');
            Route::get('show/{id}', ShuShow::class)->name('main.shu.show');
            Route::get('edit/{id}', ShuEdit::class)->name('main.shu.edit');
        });
    });
});
