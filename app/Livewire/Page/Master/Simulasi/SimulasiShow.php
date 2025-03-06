<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Models\Master\SimulasiPinjamanModel;
use App\Models\User;
use App\Models\Rbac\RoleUserModel;
use Illuminate\Support\Facades\Hash;
use App\Traits\MyAlert;

class SimulasiShow extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;
    public $tglLahir;

    public function mount($id) {
        $this->titlePage = 'Detail Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.simulasi.list'), 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = SimulasiPinjamanModel::find($id);
        $this->loadData = $data;
        $this->tglLahir = str_replace("-", "", $this->loadData['tgl_lahir']);
    }

    public function registerUser() {
        if(empty($this->loadData['tgl_lahir']) || $this->loadData['tgl_lahir'] == '') {
            $this->sweetalert([
                'icon' => 'warning',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => 'User gagal di buat, tanggal lahir belum ditambahkan/masih kosong.',
            ]);
        } else {
            try {
                $postUser = User::create([
                    'username' => $this->loadData['nomor_anggota'],
                    'email' => $this->loadData['nomor_anggota'].'@kkba.com',
                    'name' => $this->loadData['nama'],
                    'mobile' => '0899999'.$this->id,
                    'password' => Hash::make($this->tglLahir),
                    'valid_from' => $this->loadData['valid_from'],
                    'profile_photo_path' => 'avatar/blank-avatar.png'
                ]);

                if($postUser) {
                    $createRoleUser = RoleUserModel::create([
                        'role_id' => 5,
                        'user_id' => $postUser['id'],
                        'valid_from' => $this->loadData['valid_from']
                    ]);
                    if($createRoleUser) {
                        $updateAnggotaUserId = AnggotaModels::where('p_anggota_id', $this->id)->update([
                            'user_id' => $postUser['id']
                        ]);
                        if($updateAnggotaUserId) {
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
                                'text' => 'Gagal mengupdate data Anggota.',
                            ]);
                        }
                    } else {
                        $this->sweetalert([
                            'icon' => 'warning',
                            'confirmButtonText'  => 'Okay',
                            'showCancelButton' => false,
                            'text' => 'Gagal Assign Role Anggota, coba kembali.',
                        ]);
                    }
                } else {
                    $this->sweetalert([
                        'icon' => 'warning',
                        'confirmButtonText'  => 'Okay',
                        'showCancelButton' => false,
                        'text' => 'Gagal Insert data user, coba kembali.',
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
    }

    public function resetUser() {
        try {
            $post = User::where('id', $this->loadData['user_id'])->update([
                'password' => Hash::make($this->tglLahir)
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
        return view('livewire.page.master.anggota.anggota-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
