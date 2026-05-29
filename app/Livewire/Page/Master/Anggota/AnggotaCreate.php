<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Traits\MyAlert;

class AnggotaCreate extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    #component input
    public $nomor_anggota;
    public $nama;
    public $email;
    public $mobile;
    public $nik;
    public $ktp;
    public $alamat;
    public $tgl_lahir;
    public $tanggal_masuk;
    public $valid_from;
    public $valid_to;
    public $is_registered = FALSE;

    public function mount() {
        $this->titlePage = 'Tambah Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.create'), 'label' => 'Create']
        ];
    }

    public function saveInsert() {
        $validated = $this->validate([
            'nomor_anggota' => 'required|unique:p_anggota,nomor_anggota',
            'nama' => 'required|string',
            'email' => 'nullable|email:rfc,dns|unique:p_anggota,email',
            'nik' =>  'required|string|unique:p_anggota,nik',
            'mobile' => 'nullable|string|max:15|unique:p_anggota,mobile',
            'tgl_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'ktp' => 'nullable|string|max:20|unique:p_anggota,ktp',
            'valid_from' => 'required|date',
        ], [
            'nomor_anggota' => 'Nomor Anggota harus diisi.',
            'nomor_anggota.unique' => 'Nomor Anggota sudah pernah terdaftar.',
            'nama.required' => 'Nama harus diisi.',
            'nama.string' => 'Nama harus berupa string.',
            'email.email' => 'Format Email tidak valid.',
            'email.unique' => 'Email sudah pernah terdaftar.',
            'mobile.unique' => 'No HP sudah pernah terdaftar.',
            'mobile.string' => 'No HP harus berupa string.',
            'mobile.max' => 'No HP maksimal 15 karakter.',
            'nik.required' => 'NIK Anggota harus diisi.',
            'nik.unique' => 'NIK Anggota sudah pernah terdaftar',
            'tgl_lahir.required' => 'Tanggal lahir harus diisi.',
            'tgl_lahir.date' => 'Format Tanggal lahir harus "yyyy/mm/dd".',
            'tanggal_masuk.required' => 'Tanggal Masuk harus diisi.',
            'tanggal_masuk.date' => 'Format Tanggal Masuk harus "yyyy/mm/dd".',
            'valid_from.required' => 'Valid from required.',
            'valid_from.date' => 'Format Valid from harus "yyyy/mm/dd".',
            'valid_to.date' => 'Format Valid until harus "yyyy/mm/dd".',
            'ktp.max' => 'Nomor KTP maksimal 20 karakter.',
            'ktp.unique' => 'Nomor KTP sudah pernah terdaftar.',
        ]);
        if($this->valid_to == "") {
            $this->valid_to = null;
        }

        try {
            $post = AnggotaModels::create([
                'nomor_anggota' => $this->nomor_anggota,
                'nama' => $this->nama,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'nik' => $this->nik,
                'ktp' => $this->ktp,
                'tgl_lahir' => $this->tgl_lahir,
                'alamat' => $this->alamat,
                'tanggal_masuk' => $this->tanggal_masuk,
                'valid_from' => $this->valid_from,
                'valid_to' => $this->valid_to,
                'is_registered' => $this->is_registered
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
        return view('livewire.page.master.anggota.anggota-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
