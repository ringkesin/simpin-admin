<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Exception;

use App\Models\Master\AnggotaModels;
use App\Models\User;

class ProfileAnggotaController extends BaseController
{
    public function getProfile() {
        try {
            $user = Auth::user();
            $tokenAbilities = $user->currentAccessToken()->abilities;

            if (in_array('state:admin', $tokenAbilities) || in_array('state:anggota', $tokenAbilities)) {
                $userData = User::find($user->id);
                $profile = AnggotaModels::with(['atribut','unit'])
                        ->where('user_id', $user->id)
                        ->first();

                if( ! empty($userData)){
                    $userData->makeHidden([
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'created_by',
                        'updated_by',
                        'deleted_by',
                    ]);
                }

                if( ! empty($profile)){
                    $profile->makeHidden([
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

                    if( ! empty($profile->atribut)){
                        $attribute = [];
                        foreach($profile->atribut as $row) {
                            $fileUrl = NULL;
                            if ($row['atribut_attachment'] && Storage::exists($row['atribut_attachment'])) {
                                $fileUrl = URL::temporarySignedRoute(
                                    'secure-file', // Route name
                                    now()->addMinutes(1), // Expiration time
                                    ['path' => $row['atribut_attachment']] // File path parameter
                                );
                            }
                            $row['atribut_kode'] = $row->atribut_kode_beautify;
                            $row['atribut_attachment'] = $fileUrl;
                        }

                        $profile->atribut->makeHidden([
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
                    if( ! empty($profile->unit)){
                        $profile->unit->makeHidden([
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
                }
            } else {
                return $this->sendError('Access denied', ['error' => 'Anda tidak dapat mengakses data ini'], 401);
            }

            return $this->sendResponse(['profile_user' => $userData,'profile_anggota' => $profile], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'alamat_email' => 'nullable|email:rfc,dns|unique:p_anggota,email',
                'nomor_hp' => 'nullable|string|max:15|unique:p_anggota,mobile',
                'alamat' => 'nullable|string|max:1024',
                'tgl_lahir' => ['nullable', Rule::date()->format('Y-m-d'),],
                // 'profile_photo' => 'nullable|image|mimes:jpg,png|max:2048',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'alamat_email.email' => 'Alamat email tidak valid',
                'alamat_email.unique' => 'Alamat email sudah pernah terdaftar',
                'nomor_hp.unique' => 'Nomor HP sudah pernah terdaftar',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            $user = $request->user();
            $isAnggota = $user->tokenCan('state:anggota');
            $p_anggota_id = $user->anggota?->p_anggota_id;
            if ($isAnggota && (int) $request->p_anggota_id !== $p_anggota_id) {
                return response()->json(['message' => 'Tidak diizinkan mengupdate data dengan anggota id = '.$request->p_anggota_id], 403);
            }

            DB::beginTransaction();

            $data = User::with(['anggota:nomor_anggota,nama,nik,email,mobile,alamat,tgl_lahir'])->find($user->id);
            $data->email = $request->alamat_email ?? $data->email;
            $data->mobile = $request->nomor_hp ?? $data->mobile;
            // if($request->file('profile_photo')){
            //     if($data->profile_photo_path !== 'avatar/blank-avatar.png'){
            //         Storage::disk('public')->delete($data->profile_photo_path);
            //     }
            //     $path = $request->file('profile_photo')->store('avatar', 'public');
            //     $data->profile_photo_path = $path;
            // }
            $data->anggota->email = $request->alamat_email ?? $data->anggota->email;
            $data->anggota->mobile = $request->nomor_hp ?? $data->anggota->mobile;
            $data->anggota->alamat = $request->alamat ?? $data->anggota->alamat;
            $data->anggota->tgl_lahir = $request->tgl_lahir ?? $data->anggota->tgl_lahir;
            $data->updated_by = $user->id;
            $data->save();

            $data->makeHidden([
                'email_verified_at',
                'remarks',
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
                'two_factor_confirmed_at',
            ]);

            DB::commit();

            return $this->sendResponse(['update_profile' => $data], 'Update Profile Berhasil');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateProfilePhoto(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'profile_photo' => 'required|image|mimes:jpg,png|max:2048',
            ],[
                'profile_photo.required' => 'Photo Profile harus diisi',
                'profile_photo.image' => 'Photo Profile harus berupa image',
                'profile_photo.mimes' => 'Photo Profile harus berformat jpg atau png',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            $user = Auth::user();

            DB::beginTransaction();

            $data = User::find($user->id);

            if($request->file('profile_photo')){
                if($data->profile_photo_path !== 'avatar/blank-avatar.png'){
                    Storage::disk('public')->delete($data->profile_photo_path);
                }
                $path = $request->file('profile_photo')->store('avatar', 'public');
                $data->profile_photo_path = $path;
            }

            $data->save();

            $data->makeHidden([
                'email_verified_at',
                'remarks',
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
                'two_factor_confirmed_at',
            ]);

            DB::commit();

            return $this->sendResponse(['update_photo_profile' => $data], 'Update Photo Profile Berhasil');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
