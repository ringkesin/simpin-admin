<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Traits\MyAlert;

class AnggotaEdit extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;
    public $nomor_anggota;
    public $nama;
    public $nik;
    public $ktp;
    public $alamat;
    public $tgl_lahir;
    public $tanggal_masuk;
    public $valid_from;
    public $valid_to;

    public function mount($id) {
        $this->titlePage = 'Update Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('master.anggota.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = AnggotaModels::find($id);
        $this->loadData = $data;
        $this->nomor_anggota = $this->loadData['nomor_anggota'];
        $this->nama = $this->loadData['nama'];
        $this->nik = $this->loadData['nik'];
        $this->ktp = $this->loadData['ktp'];
        $this->alamat = $this->loadData['alamat'];
        $this->tgl_lahir = $this->loadData['tgl_lahir'];
        $this->tanggal_masuk = $this->loadData['tanggal_masuk'];
        $this->valid_from = $this->loadData['valid_from'];
        $this->valid_to = $this->loadData['valid_to'];
    }

    public function saveUpdate() {
        $validated = $this->validate([
            'nomor_anggota' => 'required',
            'nama' => 'required',
            'nik' =>  'required',
            'tgl_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'valid_from' => 'required|date',
        ], [
            'nomor_anggota.required' => 'Nomor Anggota required',
            'nama.required' => 'User required.',
            'nik.required' => 'ID Sign required.',
            'tgl_lahir.required' => 'Tanggal lahir required.',
            'tgl_lahir.date' => 'Format Tanggal lahir must "yyyy/mm/dd".',
            'tanggal_masuk.required' => 'Tanggal Masuk required.',
            'tanggal_masuk.date' => 'Format Tanggal Masuk must "yyyy/mm/dd".',
            'valid_from.required' => 'Valid from required.',
            'valid_from.date' => 'Format Valid from must "yyyy/mm/dd".',
            'valid_to.date' => 'Format Valid until must "yyyy/mm/dd".',
        ]);
        if($this->valid_to == "") {
            $this->valid_to = null;
        }

        $redirect = route('master.anggota.list');

        try {
            $post = AnggotaModels::where('p_anggota_id', $this->id)->update([
                'nomor_anggota' => $this->nomor_anggota,
                'nama' => $this->nama,
                'nik' => $this->nik,
                'ktp' => $this->ktp,
                'tgl_lahir' => $this->tgl_lahir,
                'alamat' => $this->alamat,
                'tanggal_masuk' => $this->tanggal_masuk,
                'valid_from' => $this->valid_from,
                'valid_to' => $this->valid_to
            ]);

            if($post) {
                $redirect = route('master.anggota.show', ['id' => $post]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }
        } catch (QueryException $e) {
            $textError = '';
            if($e->errorInfo[1] == 1062) {
                $textError = 'Data gagal di update karena duplikat data, coba kembali.';
            } else {
                $textError = 'Data gagal di update, coba kembali.';
            }
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
