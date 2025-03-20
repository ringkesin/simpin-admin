<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Master\AnggotaModels;
use App\Models\Master\AnggotaAtributModels;
use Exception;

class AnggotaController extends BaseController
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'alamat_email' => 'required|email:rfc,dns|unique:p_anggota,email',
                'nomor_hp' => 'nullable|string|max:15|unique:p_anggota,mobile',
                'nomor_pegawai' => 'required|string|max:15|unique:p_anggota,nik',
                'nomor_ktp' => 'nullable|string|max:20|unique:p_anggota,ktp',
                'tempat_lahir' => 'nullable|string|max:64',
                'tanggal_lahir' => ['required', Rule::date()->format('Y-m-d'),],
                'alamat' => 'nullable|string|max:1024',
                'p_unit_id' => 'required|integer',
                'attachment_ktp' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'attachment_kartu_pegawai' => 'required|file|mimes:jpg,png,pdf|max:2048',
            ],[
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
                'alamat_email.unique' => 'Alamat email sudah pernah terdaftar',
                'nomor_hp.unique' => 'Nomor HP sudah pernah terdaftar',
                'nomor_pegawai.unique' => 'Nomor Pegawai sudah pernah terdaftar',
                'nomor_ktp.unique' => 'Nomor KTP sudah pernah terdaftar',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $lastAnggota = AnggotaModels::latest('nomor_anggota')->first();
            $newNomorAnggota = $lastAnggota ? $lastAnggota->nomor_anggota + 1 : 100001; 

            $ktpPath = $request->file('attachment_ktp')->store('uploads/ktp', 'local');
            $employeeCardIdPath = $request->file('attachment_kartu_pegawai')->store('uploads/kartu_pegawai', 'local');

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
                'atribut_attachment' => $ktpPath
            ]);

            AnggotaAtributModels::create([
                'p_anggota_id' => $anggota->p_anggota_id,
                'atribut_kode' => 'kartu_pegawai',
                'atribut_value' => $request->nomor_pegawai,
                'atribut_attachment' => $employeeCardIdPath
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
                $anggota = AnggotaModels::with(['atribut','unit'])->where('p_anggota_id', $p_anggota_id)->first();
            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::with(['atribut','unit'])
                    ->where('p_anggota_id', $p_anggota_id)
                    ->where('user_id', $user->id) 
                    ->first();
            } else {
                return $this->sendError('Access denied', ['error' => 'Anda tidak dapat mengakses data ini'], 401);
            }

            if ( ! $anggota) {
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
            if( ! empty($anggota->atribut)){
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
            if( ! empty($anggota->unit)){
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