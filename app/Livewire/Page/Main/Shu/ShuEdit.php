<?php

namespace App\Livewire\Page\Main\Shu;

use Livewire\Component;
use Illuminate\Database\QueryException;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ShuModels;
use App\Models\Master\AnggotaModels;

class ShuEdit extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $loadDataAnggota = [];

    #component input
    public $id;
    public $p_anggota_id;
    public $tahun;
    public $shu_diterima;
    public $shu_dibagi;
    public $shu_ditabung;
    public $shu_tahun_lalu;

    public function mount($id) {
        $this->titlePage = 'Update SHU Anggota';
        $this->menuCode = 'shu';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'SHU'],
            ['link' => route('main.shu.list'), 'label' => 'List'],
            ['link' => route('main.shu.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('main.shu.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
        $this->getAnggota();
    }

    public function getData($id) {
        $data = ShuModels::find($id);
        $this->loadData = $data;
        $this->p_anggota_id = $this->loadData['p_anggota_id'];
        $this->tahun = $this->loadData['tahun'];
        $this->shu_diterima = $this->loadData['shu_diterima'];
        $this->shu_dibagi = $this->loadData['shu_dibagi'];
        $this->shu_ditabung = $this->loadData['shu_ditabung'];
        $this->shu_tahun_lalu = $this->loadData['shu_tahun_lalu'];
    }

    public function getAnggota() {
        $data = AnggotaModels::all();
        $this->loadDataAnggota = $data;
    }

    public function saveUpdate() {
        $validated = $this->validate([
            'p_anggota_id' => 'required',
            'tahun' => 'required',
            'shu_diterima' =>  'required',
            'shu_dibagi' => 'required',
            'shu_ditabung' => 'required',
            'shu_tahun_lalu' => 'required'
        ], [
            'p_anggota_id' => 'Nama Anggota required',
            'tahun.required' => 'Tahun required.',
            'shu_diterima.required' => 'SHU Diterima required.',
            'shu_dibagi.required' => 'SHU Dibagi required.',
            'shu_ditabung.required' => 'SHU Ditabung required.',
            'shu_tahun_lalu.required' => 'SHU Tahun Lalu required'
        ]);

        try {
            $checkDuplicatePeriod = ShuModels::where([
                ['tahun', '=', $this->tahun],
                ['p_anggota_id', '=', $this->p_anggota_id],
                ['t_shu_id', '!=', $this->id]
            ])->count();

            if($checkDuplicatePeriod == 0) {
                $post = ShuModels::where('t_shu_id', $this->id)->update([
                    'p_anggota_id' => $this->p_anggota_id,
                    'tahun' => $this->tahun,
                    'shu_diterima' => $this->shu_diterima,
                    'shu_dibagi' => $this->shu_dibagi,
                    'shu_ditabung' => $this->shu_ditabung,
                    'shu_tahun_lalu' => $this->shu_tahun_lalu,
                ]);

                if($post) {
                    $redirect = route('main.shu.show', ['id' => $this->id]);
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
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, Terdapat Duplikat Data (SHU Anggota hanya bisa diinput 1 kali dalam 1 tahun).',
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
        return view('livewire.page.main.shu.shu-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
