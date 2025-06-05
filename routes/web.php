<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Page\Dashboard;
use App\Livewire\Page\Master\Anggota\AnggotaList;
use App\Livewire\Page\Master\Anggota\AnggotaCreate;
use App\Livewire\Page\Master\Anggota\AnggotaShow;
use App\Livewire\Page\Master\Anggota\AnggotaEdit;

use App\Livewire\Page\Main\Tabungan\TabunganList;
use App\Livewire\Page\Main\Tabungan\TabunganCreate;
// use App\Livewire\Page\Main\Tabungan\TabunganShow;
use App\Livewire\Page\Main\Tabungan\TabunganUpdate;
use App\Livewire\Page\Main\Tabungan\TabunganEdit;
use App\Livewire\Page\Main\Tabungan\TabunganImport;

use App\Livewire\Page\Main\Pencairan\PencairanTabunganList;
use App\Livewire\Page\Main\Pencairan\PencairanTabunganApproval;

use App\Livewire\Page\Main\Tagihan\TagihanList;
use App\Livewire\Page\Main\Tagihan\TagihanCreate;
use App\Livewire\Page\Main\Tagihan\TagihanShow;
use App\Livewire\Page\Main\Tagihan\TagihanEdit;
use App\Livewire\Page\Main\Tagihan\TagihanImport;

use App\Livewire\Page\Main\Pinjaman\PinjamanList;
use App\Livewire\Page\Main\Pinjaman\PinjamanCreate;
use App\Livewire\Page\Main\Pinjaman\PinjamanShow;

use App\Livewire\Page\Main\Shu\ShuList;
use App\Livewire\Page\Main\Shu\ShuCreate;
use App\Livewire\Page\Main\Shu\ShuShow;
use App\Livewire\Page\Main\Shu\ShuEdit;
use App\Livewire\Page\Main\Shu\ShuImport;

use App\Livewire\Page\Main\Konten\KontenList;
use App\Livewire\Page\Main\Konten\KontenCreate;
use App\Livewire\Page\Main\Konten\KontenShow;
use App\Livewire\Page\Main\Konten\KontenEdit;

use App\Livewire\Page\User\UserList;
use App\Livewire\Page\User\UserEdit;
use App\Livewire\Page\User\UserCreate;
use App\Livewire\Page\User\UserShow;

use App\Livewire\Page\Master\Simulasi\SimulasiList;
use App\Livewire\Page\Master\Simulasi\SimulasiShow;
use App\Livewire\Page\Master\Simulasi\SimulasiCreate;
use App\Livewire\Page\Master\Simulasi\SimulasiEdit;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });
// Route::redirect('/', 'login');

Route::redirect('/', 'login');

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
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
            Route::get('create', SimulasiCreate::class)->name('master.simulasi.create');
            Route::get('show/{id}', SimulasiShow::class)->name('master.simulasi.show');
            Route::get('edit/{id}', SimulasiEdit::class)->name('master.simulasi.edit');
        });
    });
    Route::prefix('main')->group(function () {
        Route::prefix('tabungan')->group(function () {
            Route::get('list', TabunganList::class)->name('main.tabungan.list');
            // Route::get('create', TabunganCreate::class)->name('main.tabungan.create');
            // Route::get('import', TabunganImport::class)->name('main.tabungan.import');
            Route::get('update/{id}', TabunganUpdate::class)->name('main.tabungan.update');
        });
        Route::prefix('pencairan')->group(function () {
            Route::get('list', PencairanTabunganList::class)->name('main.pencairan.list');
            Route::get('approval/{id}', PencairanTabunganApproval::class)->name('main.pencairan.approval');
        });
        Route::prefix('tagihan')->group(function () {
            Route::get('list', TagihanList::class)->name('main.tagihan.list');
            Route::get('create', TagihanCreate::class)->name('main.tagihan.create');
            Route::get('import', TagihanImport::class)->name('main.tagihan.import');
            Route::get('show/{id}', TagihanShow::class)->name('main.tagihan.show');
            Route::get('edit/{id}', TagihanEdit::class)->name('main.tagihan.edit');
        });
        Route::prefix('shu')->group(function () {
            Route::get('list', ShuList::class)->name('main.shu.list');
            Route::get('create', ShuCreate::class)->name('main.shu.create');
            Route::get('import', ShuImport::class)->name('main.shu.import');
            Route::get('show/{id}', ShuShow::class)->name('main.shu.show');
            Route::get('edit/{id}', ShuEdit::class)->name('main.shu.edit');
        });
        Route::prefix('pinjaman')->group(function () {
            Route::get('list', PinjamanList::class)->name('main.pinjaman.list');
            Route::get('create', PinjamanCreate::class)->name('main.pinjaman.create');
            Route::get('show/{id}', PinjamanShow::class)->name('main.pinjaman.show');
        });
        Route::prefix('konten')->group(function () {
            Route::get('list', KontenList::class)->name('main.konten.list');
            Route::get('create', KontenCreate::class)->name('main.konten.create');
            Route::get('show/{id}', KontenShow::class)->name('main.konten.show');
            Route::get('edit/{id}', KontenEdit::class)->name('main.konten.edit');
        });
    });
});
