<?php

namespace App\Livewire\Page\Master\Anggota;

use App\Models\Master\AnggotaAtributModels;
use App\Models\Master\AnggotaModels;
use App\Models\Rbac\RoleModel;
use App\Models\Rbac\RoleUserModel;
use App\Models\User;
use App\Services\AnggotaRegistrationService;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class AnggotaShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;

    public $titlePage;

    public $menuCode;

    public $loadData = [];

    public $loadDataAttr = [];

    public $id;

    public $tglLahir;

    public function mount($id)
    {
        $this->titlePage = 'Detail Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.show', ['id' => $id]), 'label' => 'Show'],
        ];

        $this->id = $id;
        $this->getData($id);
        $this->getDataAttr($id);
    }

    public function getData($id)
    {
        $data = AnggotaModels::withTrashed()->findOrFail($id);
        $this->loadData = $data->toArray();
        $this->tglLahir = str_replace('-', '', $this->loadData['tgl_lahir'] ?? '');
    }

    public function getDataAttr($id)
    {
        $data = AnggotaAtributModels::where('p_anggota_id', '=', $id)->get();
        $attribute = [];
        foreach ($data as $d) {

            $fileUrl = null;
            if (Storage::disk('kkba_simpin')->exists($d->atribut_attachment)) {
                $fileUrl = URL::temporarySignedRoute(
                    'secure-file', // Route name
                    now()->addMinutes(1), // Expiration time
                    ['path' => $d->atribut_attachment] // File path parameter
                );
            }

            $attribute[] = [
                'atribut_kode' => $d->atribut_kode_beautify,
                'atribut_value' => $d->atribut_value,
                'atribut_attachment' => $fileUrl,
            ];
        }
        $this->loadDataAttr = $attribute;
    }

    public function registerUser()
    {
        if (! empty($this->loadData['deleted_at']) || empty($this->loadData['is_registered']) || empty($this->loadData['nomor_anggota'])) {
            $this->sweetalert([
                'icon' => 'warning',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'User hanya dapat dibuat untuk anggota yang sudah disetujui.',
            ]);
        } elseif (empty($this->loadData['tgl_lahir']) || $this->loadData['tgl_lahir'] == '') {
            $this->sweetalert([
                'icon' => 'warning',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'User gagal di buat, tanggal lahir belum ditambahkan/masih kosong.',
            ]);
        } else {
            try {
                DB::transaction(function () {
                    $anggota = AnggotaModels::whereKey($this->id)->lockForUpdate()->firstOrFail();

                    if (! $anggota->is_registered || ! $anggota->nomor_anggota || $anggota->user_id) {
                        throw new \RuntimeException('Status anggota tidak memenuhi syarat untuk membuat user.');
                    }

                    $roleAnggota = RoleModel::where('code', 'mobile_anggota')->firstOrFail();
                    $postUser = User::create([
                        'username' => $anggota->nomor_anggota,
                        'email' => $this->setIfNull($anggota->email, $anggota->nomor_anggota.'@kkba.com'),
                        'name' => $anggota->nama,
                        'mobile' => $this->setIfNull($anggota->mobile, '0899999'.$this->id),
                        'password' => Hash::make($this->tglLahir),
                        'valid_from' => $anggota->valid_from,
                        'profile_photo_path' => 'avatar/blank-avatar.png',
                    ]);

                    RoleUserModel::create([
                        'role_id' => $roleAnggota->id,
                        'user_id' => $postUser->id,
                        'valid_from' => $anggota->valid_from,
                        'created_by' => auth()->id(),
                    ]);

                    $anggota->user_id = $postUser->id;
                    $anggota->updated_by = auth()->id();
                    $anggota->save();
                });

                $redirect = route('master.anggota.show', ['id' => $this->id]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'User anggota berhasil dibuat.',
                    'redirectUrl' => $redirect,
                ]);
            } catch (\Throwable $e) {
                $textError = '';
                if ($e instanceof QueryException && ($e->errorInfo[1] ?? null) == 1062) {
                    $textError = 'Data gagal di update karena duplikat data, coba kembali.';
                } else {
                    $textError = 'Data gagal di update, coba kembali. '.$e->getMessage();
                }
                $this->sweetalert([
                    'icon' => 'error',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => $textError,
                ]);
            }

        }
    }

    public function resetUser()
    {
        if (! empty($this->loadData['deleted_at']) || empty($this->loadData['user_id'])) {
            return;
        }

        try {
            $post = User::where('id', $this->loadData['user_id'])->update([
                'password' => Hash::make($this->tglLahir),
            ]);

            if ($post) {
                $redirect = route('master.anggota.show', ['id' => $this->id]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect,
                ]);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }
        } catch (QueryException $e) {
            $textError = '';
            if ($e->errorInfo[1] == 1062) {
                $textError = 'Data gagal di update karena duplikat data, coba kembali.';
            } else {
                $textError = 'Data gagal di update, coba kembali.';
            }
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }

    public function rejectRegistration()
    {
        try {
            app(AnggotaRegistrationService::class)->reject((int) $this->id, auth()->id());

            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Registrasi anggota berhasil ditolak.',
                'redirectUrl' => route('master.anggota.show', ['id' => $this->id]),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->sweetalert([
                'icon' => 'warning',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => collect($e->errors())->flatten()->first(),
            ]);
        } catch (\Throwable $e) {
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Registrasi gagal ditolak, coba kembali.',
            ]);
        }
    }

    public function deleteMember()
    {
        try {
            DB::transaction(function () {
                $anggota = AnggotaModels::whereKey($this->id)->lockForUpdate()->firstOrFail();

                if (! $anggota->nomor_anggota) {
                    throw new \RuntimeException('Registrasi tanpa nomor anggota harus menggunakan aksi reject.');
                }

                if ($anggota->user_id) {
                    $user = User::whereKey($anggota->user_id)->lockForUpdate()->firstOrFail();
                    $user->tokens()->delete();
                    $user->deleted_by = auth()->id();
                    $user->save();
                    $user->delete();
                }

                $anggota->deleted_by = auth()->id();
                $anggota->save();
                $anggota->delete();
            });

            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data anggota berhasil dihapus.',
                'redirectUrl' => route('master.anggota.list'),
            ]);
        } catch (\Throwable $e) {
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data gagal dihapus: '.$e->getMessage(),
            ]);
        }
    }

    public function restoreMember()
    {
        try {
            app(AnggotaRegistrationService::class)->restore((int) $this->id, auth()->id());

            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Anggota berhasil diaktifkan kembali.',
                'redirectUrl' => route('master.anggota.show', ['id' => $this->id]),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->sweetalert([
                'icon' => 'warning',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => collect($e->errors())->flatten()->first(),
            ]);
        } catch (\Throwable $e) {
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Anggota gagal diaktifkan kembali, coba kembali.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-show')
            ->layoutData([
                'title' => $this->titlePage, // Page Title
                'breadcrumbs' => $this->breadcrumb,
                'menu_code' => $this->menuCode,
            ]);
    }
}
