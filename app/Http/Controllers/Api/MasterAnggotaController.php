<?php

namespace App\Http\Controllers\Api;

use App\Models\Master\AnggotaAtributModels;
use App\Models\Master\AnggotaModels;
use App\Models\Rbac\RoleModel;
use App\Models\Rbac\RoleUserModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MasterAnggotaController extends BaseController
{
    /**
     * Menampilkan calon anggota dari pendaftaran publik yang belum disetujui.
     * Akses endpoint ini dibatasi pada token dengan ability state:admin.
     */
    public function getRegistrasiBaru(Request $request)
    {
        try {
            $page = max((int) $request->input('page', 1), 1);
            $perPage = min(max((int) $request->input('perpage', 10), 1), 100);
            $offset = ($page - 1) * $perPage;
            $data = $request->input('data', []);

            $anggota = AnggotaModels::with(['atribut', 'unit'])
                ->where('is_registered', false)
                ->whereNull('user_id')
                ->whereNull('deleted_at');

            if (isset($data['search']) && $data['search'] !== '') {
                $search = $data['search'];

                $anggota->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nomor_anggota', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('ktp', 'like', "%{$search}%");
                });
            }

            if (isset($data['p_unit_id'])) {
                $anggota->where('p_unit_id', $data['p_unit_id']);
            }

            if (isset($data['tanggal_daftar_dari'])) {
                $anggota->whereDate('created_at', '>=', $data['tanggal_daftar_dari']);
            }

            if (isset($data['tanggal_daftar_sampai'])) {
                $anggota->whereDate('created_at', '<=', $data['tanggal_daftar_sampai']);
            }

            $anggota = $anggota
                ->offset($offset)
                ->limit($perPage)
                ->latest('created_at')
                ->get();

            if ($anggota->isEmpty()) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            return $this->sendResponse($anggota, 'Data registrasi baru berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyetujui pendaftaran calon anggota tanpa membuat akun login.
     * Akses endpoint ini dibatasi pada token dengan ability state:admin.
     */
    public function setujuiRegistrasi(Request $request, $p_anggota_id)
    {
        try {
            $anggota = AnggotaModels::where('p_anggota_id', $p_anggota_id)
                ->whereNull('deleted_at')
                ->first();

            if (! $anggota) {
                return $this->sendError('Not Found', ['error' => 'Data anggota tidak ditemukan'], 404);
            }

            if (! $anggota->is_registered) {
                $anggota->update([
                    'is_registered' => true,
                    'updated_by' => $request->user()->id,
                ]);
            }

            return $this->sendResponse(['anggota' => $anggota->fresh()], 'Registrasi anggota berhasil disetujui.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Membuat akun mobile untuk anggota yang pendaftarannya telah disetujui.
     * Username memakai nomor anggota dan password awal memakai tanggal lahir (Ymd).
     */
    public function daftarUser(Request $request, $p_anggota_id)
    {
        try {
            DB::beginTransaction();

            $anggota = AnggotaModels::where('p_anggota_id', $p_anggota_id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (! $anggota) {
                DB::rollBack();

                return $this->sendError('Not Found', ['error' => 'Data anggota tidak ditemukan'], 404);
            }

            if (! $anggota->is_registered) {
                DB::rollBack();

                return $this->sendError('Registrasi belum disetujui', ['error' => 'Setujui registrasi anggota terlebih dahulu'], 422);
            }

            if ($anggota->user_id) {
                DB::rollBack();

                return $this->sendError('User sudah terdaftar', ['error' => 'Anggota ini sudah memiliki akun user'], 422);
            }

            if (empty($anggota->tgl_lahir)) {
                DB::rollBack();

                return $this->sendError('Tanggal lahir belum tersedia', ['error' => 'Tanggal lahir diperlukan untuk membuat password awal'], 422);
            }

            $email = $anggota->email ?: $anggota->nomor_anggota.'@kkba.com';
            $mobile = $anggota->mobile ?: '0899999'.$anggota->p_anggota_id;
            $userExists = User::where('username', $anggota->nomor_anggota)
                ->orWhere('email', $email)
                ->orWhere('mobile', $mobile)
                ->exists();

            if ($userExists) {
                DB::rollBack();

                return $this->sendError('User tidak dapat dibuat', ['error' => 'Username, email, atau nomor ponsel sudah digunakan'], 422);
            }

            $roleAnggota = RoleModel::where('code', 'mobile_anggota')->first();

            if (! $roleAnggota) {
                DB::rollBack();

                return $this->sendError('Role tidak ditemukan', ['error' => 'Role mobile_anggota belum tersedia'], 500);
            }

            $user = User::create([
                'username' => $anggota->nomor_anggota,
                'email' => $email,
                'name' => $anggota->nama,
                'mobile' => $mobile,
                'password' => Hash::make(str_replace('-', '', $anggota->tgl_lahir)),
                'valid_from' => $anggota->valid_from,
                'profile_photo_path' => 'avatar/blank-avatar.png',
            ]);

            RoleUserModel::create([
                'role_id' => $roleAnggota->id,
                'user_id' => $user->id,
                'valid_from' => $anggota->valid_from,
                'created_by' => $request->user()->id,
            ]);

            $anggota->update([
                'user_id' => $user->id,
                'updated_by' => $request->user()->id,
            ]);

            DB::commit();

            return $this->sendResponse([
                'user' => $user,
                'anggota' => $anggota->fresh(),
            ], 'User anggota berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'alamat_email' => 'required|email:rfc,dns',
                'nomor_hp' => 'nullable|string|max:15',
                'nomor_pegawai' => 'required|string|max:15',
                'nomor_ktp' => 'nullable|string|max:20',
                'tempat_lahir' => 'nullable|string|max:64',
                'tanggal_lahir' => ['required', Rule::date()->format('Y-m-d')],
                'alamat' => 'nullable|string|max:1024',
                'p_unit_id' => 'required|integer',
                'attachment_ktp' => 'required|file|mimes:jpg,png|max:2048',
                'attachment_kartu_pegawai' => 'required|file|mimes:jpg,png|max:2048',
            ], [
                'nama.required' => 'Nama harus diisi',
                'alamat_email.required' => 'Email harus diisi',
                // 'nomor_hp.required' => 'Nomor HP harus diisi',
                'nomor_pegawai.required' => 'Nomor Pegawai harus diisi',
                // 'nomor_ktp.required' => 'Nomor KTP harus diisi',
                // 'tempat_lahir.required' => 'Tempat Lahir harus diisi',
                'tanggal_lahir.required' => 'Tanggal Lahir harus diisi',
                // 'alamat.required' => 'Alamat tinggal harus diisi',
                'p_unit_id.required' => 'Unit / Proyek harus diisi',
                'attachment_ktp.required' => 'Attachment KTP harus diisi',
                'attachment_kartu_pegawai.required' => 'Attachment Kartu Pegawai (ID Card) harus diisi',
                'alamat_email.email' => 'Alamat email tidak valid',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            $anggotaTerdaftar = AnggotaModels::whereNull('deleted_at')
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->alamat_email)
                        ->orWhere('nik', $request->nomor_pegawai);

                    if ($request->filled('nomor_hp')) {
                        $query->orWhere('mobile', $request->nomor_hp);
                    }

                    if ($request->filled('nomor_ktp')) {
                        $query->orWhere('ktp', $request->nomor_ktp);
                    }
                })
                ->orderByDesc('is_registered')
                ->first();

            if ($anggotaTerdaftar) {
                $message = $anggotaTerdaftar->is_registered
                    ? 'Anda sudah terdaftar sebagai anggota, silahkan hubungi admin KKBA untuk konfirmasi data keanggotaan anda.'
                    : 'Anda sudah ter registrasi, silahkan hubungi admin KKBA untuk konfirmasi data keanggotaan anda.';

                return $this->sendError($message, [], 409);
            }

            DB::beginTransaction();

            $lastAnggota = AnggotaModels::latest('nomor_anggota')->first();
            $newNomorAnggota = $lastAnggota ? $lastAnggota->nomor_anggota + 1 : 100001;

            $ktpPath = $request->file('attachment_ktp')->store('uploads/ktp', 'kkba_simpin');
            $employeeCardIdPath = $request->file('attachment_kartu_pegawai')->store('uploads/kartu_pegawai', 'kkba_simpin');

            $anggota = AnggotaModels::create([
                'nama' => $request->nama,
                'nomor_anggota' => $newNomorAnggota,
                'valid_from' => date('Y-m-d'),
                'tanggal_masuk' => date('Y-m-d'),
                'email' => $request->alamat_email,
                'mobile' => $request->nomor_hp,
                'nik' => $request->nomor_pegawai,
                'ktp' => $request->nomor_ktp,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'p_unit_id' => $request->p_unit_id,
                'is_registered' => false,
            ]);

            AnggotaAtributModels::create([
                'p_anggota_id' => $anggota->p_anggota_id,
                'atribut_kode' => 'ktp',
                'atribut_value' => $request->nomor_ktp,
                'atribut_attachment' => $ktpPath,
            ]);

            AnggotaAtributModels::create([
                'p_anggota_id' => $anggota->p_anggota_id,
                'atribut_kode' => 'kartu_pegawai',
                'atribut_value' => $request->nomor_pegawai,
                'atribut_attachment' => $employeeCardIdPath,
            ]);

            DB::commit();

            return $this->sendResponse(['anggota' => $anggota], 'Registrasi Anggota Berhasil');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getAnggotaById($p_anggota_id)
    {
        try {
            $user = Auth::user();
            $tokenAbilities = $user->currentAccessToken()->abilities;

            if (in_array('state:admin', $tokenAbilities)) {
                $anggota = AnggotaModels::with(['atribut', 'unit'])->where('p_anggota_id', $p_anggota_id)->first();
            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::with(['atribut', 'unit'])
                    ->where('p_anggota_id', $p_anggota_id)
                    ->where('user_id', $user->id)
                    ->first();
            } else {
                return $this->sendError('Access denied', ['error' => 'Anda tidak dapat mengakses data ini'], 401);
            }

            if (! $anggota) {
                return $this->sendError('Not Found', ['error' => 'Data tidak ditemukan'], 404);
            }

            $anggota->makeHidden([
                'valid_from',
                'valid_to',
                'p_jenis_kelamin_id',
                'p_company_id',
                'p_unit_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
            if (! empty($anggota->atribut)) {
                $anggota->atribut->makeHidden([
                    'p_anggota_atribut_id',
                    'p_anggota_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                ]);
            }
            if (! empty($anggota->unit)) {
                $anggota->unit->makeHidden([
                    'parent_id',
                    'location',
                    'longitude',
                    'latitude',
                    'is_project',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                ]);
            }

            return $this->sendResponse(['anggota' => $anggota], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
