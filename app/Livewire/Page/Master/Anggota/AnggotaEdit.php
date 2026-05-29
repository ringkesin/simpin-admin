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
    public $email;
    public $mobile;
    public $nik;
    public $ktp;
    public $alamat;
    public $tgl_lahir;
    public $tanggal_masuk;
    public $valid_from;
    public $valid_to;
    public $is_registered;

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
        $this->email = $this->loadData['email'];
        $this->mobile = $this->loadData['mobile'];
        $this->nik = $this->loadData['nik'];
        $this->ktp = $this->loadData['ktp'];
        $this->alamat = $this->loadData['alamat'];
        $this->tgl_lahir = $this->loadData['tgl_lahir'];
        $this->tanggal_masuk = $this->loadData['tanggal_masuk'];
        $this->valid_from = $this->loadData['valid_from'] ? date('Y-m-d', strtotime($this->loadData['valid_from'])) : NULL;
        $this->valid_to = $this->loadData['valid_to'] ? date('Y-m-d', strtotime($this->loadData['valid_to'])) : NULL;
        $this->is_registered = $this->loadData['is_registered'];
    }

    public function saveUpdate() {
        $validated = $this->validate([
            'nomor_anggota' => 'required|unique:p_anggota,nomor_anggota,'.$this->id.',p_anggota_id',
            'nama' => 'required|string',
            'email' => 'nullable|email:rfc,dns|unique:p_anggota,email,'.$this->id.',p_anggota_id',
            'nik' =>  'required|string|unique:p_anggota,nik,'.$this->id.',p_anggota_id',
            'mobile' => 'nullable|string|max:15|unique:p_anggota,mobile,'.$this->id.',p_anggota_id',
            'tgl_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'ktp' => 'nullable|string|max:20|unique:p_anggota,ktp,'.$this->id.',p_anggota_id',
            'valid_from' => 'required|date',
            'valid_to' => 'date|nullable'
        ], [
            'nomor_anggota.required' => 'Nomor Anggota required',
            'nomor_anggota.unique' => 'Nomor Anggota sudah pernah terdaftar.',
            'nama.required' => 'Nama required.',
            'nama.string' => 'Nama harus berupa string.',
            'email.email' => 'Format Email tidak valid.',
            'email.unique' => 'Email sudah pernah terdaftar.',
            'mobile.string' => 'No HP harus berupa string.',
            'mobile.max' => 'No HP maksimal 15 karakter.',
            'mobile.unique' => 'No HP sudah pernah terdaftar.',
            'nik.required' => 'NIK required.',
            'nik.string' => 'NIK harus berupa string.',
            'tgl_lahir.required' => 'Tanggal lahir required.',
            'tgl_lahir.date' => 'Format Tanggal lahir must "yyyy/mm/dd".',
            'tanggal_masuk.required' => 'Tanggal Masuk required.',
            'tanggal_masuk.date' => 'Format Tanggal Masuk must "yyyy/mm/dd".',
            'valid_from.required' => 'Valid from required.',
            'valid_from.date' => 'Format Valid from must "yyyy/mm/dd".',
            'valid_to.date' => 'Format Valid until must "yyyy/mm/dd".',
            'ktp.max' => 'Nomor KTP maksimal 20 karakter.',
            'ktp.unique' => 'Nomor KTP sudah pernah terdaftar.',
        ]);
        if($this->valid_to == "") {
            $this->valid_to = null;
        }

        $redirect = route('master.anggota.list');

        try {
            $post = AnggotaModels::where('p_anggota_id', $this->id)->update([
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
                $redirect = route('master.anggota.show', ['id' => $this->id]);
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
