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
use App\Models\Master\AnggotaAtributModels;
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
                'alamat_email' => [
                    'nullable',
                    'email:rfc,dns',
                    Rule::unique('p_anggota', 'email')->ignore($request->p_anggota_id, 'p_anggota_id'),
                ],
                'nomor_hp' => [
                    'nullable',
                    'string',
                    'max:15',
                    Rule::unique('p_anggota', 'mobile')->ignore($request->p_anggota_id, 'p_anggota_id'),
                ],
                'alamat' => 'nullable|string|max:1024',
                'tgl_lahir' => ['required', Rule::date()->format('Y-m-d'),],
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

            $data = User::find($user->id);
            $data->email = $request->alamat_email ?? $data->email;
            $data->mobile = $request->nomor_hp ?? $data->mobile;
            // if($request->file('profile_photo')){
            //     if($data->profile_photo_path !== 'avatar/blank-avatar.png'){
            //         Storage::disk('public')->delete($data->profile_photo_path);
            //     }
            //     $path = $request->file('profile_photo')->store('avatar', 'public');
            //     $data->profile_photo_path = $path;
            // }
            $data->updated_by = $user->id;
            $data->save();

            $anggota = AnggotaModels::find($request->p_anggota_id);
            $anggota->email = $request->alamat_email;
            $anggota->mobile = $request->nomor_hp;
            $anggota->alamat = $request->alamat;
            $anggota->tgl_lahir = $request->tgl_lahir;
            $anggota->updated_by = $user->id;
            $anggota->save();

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

    public function getAttrDoc() {
        try {
            $user = Auth::user();

            $p_anggota_id = $user->anggota?->p_anggota_id;

            if(!$p_anggota_id) {
                return response()->json(['message' => 'Unauthorize.'], 403);
            }

            // Check attribut
            $checkAtt = AnggotaAtributModels::where('p_anggota_id', $p_anggota_id)->get();

            $ktpAttribute = $checkAtt->firstWhere('atribut_kode', 'ktp');
            $kartuPegawaiAttribute = $checkAtt->firstWhere('atribut_kode', 'kartu_pegawai');
            $kkAttribute = $checkAtt->firstWhere('atribut_kode', 'kartu_keluarga');
            $npwpAttribute = $checkAtt->firstWhere('atribut_kode', 'npwp');
            $bukuNikahAttribute = $checkAtt->firstWhere('atribut_kode', 'buku_nikah');

            $data = [
                'attr_no_ktp' => null,
                'attachment_ktp' => null,
                'attr_no_kartu_pegawai' => null,
                'attachment_kartu_pegawai' => null,
                'attr_no_kartu_keluarga' => null,
                'attachment_kartu_keluarga' => null,
                'attr_npwp' => null,
                'attachment_npwp' => null,
                'attr_buku_nikah' => null,
                'attachment_buku_nikah' => null,
            ];

            if($ktpAttribute) {
                $fileKtpUrl = NULL;
                if ($ktpAttribute['atribut_attachment'] && Storage::exists($ktpAttribute['atribut_attachment'])) {
                    $fileKtpUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $ktpAttribute['atribut_attachment']] // File path parameter
                    );
                }
                $data['attr_no_ktp'] = $ktpAttribute['atribut_value'];
                $data['attachment_ktp'] = $fileKtpUrl;
            }

            if($kartuPegawaiAttribute) {
                $fileKPUrl = NULL;
                if ($kartuPegawaiAttribute['atribut_attachment'] && Storage::exists($kartuPegawaiAttribute['atribut_attachment'])) {
                    $fileKPUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $kartuPegawaiAttribute['atribut_attachment']] // File path parameter
                    );
                }
                $data['attr_no_kartu_pegawai'] = $kartuPegawaiAttribute['atribut_value'];
                $data['attachment_kartu_pegawai'] = $fileKPUrl;
            }

            if($kkAttribute) {
                $fileKKUrl = NULL;
                if ($kkAttribute['atribut_attachment'] && Storage::exists($kkAttribute['atribut_attachment'])) {
                    $fileKKUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $kkAttribute['atribut_attachment']] // File path parameter
                    );
                }
                $data['attr_no_kartu_keluarga'] = $kkAttribute['atribut_value'];
                $data['attachment_kartu_keluarga'] = $fileKKUrl;
            }

            if($npwpAttribute) {
                $fileNpwpUrl = NULL;
                if ($npwpAttribute['atribut_attachment'] && Storage::exists($npwpAttribute['atribut_attachment'])) {
                    $fileNpwpUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $npwpAttribute['atribut_attachment']] // File path parameter
                    );
                }
                $data['attr_npwp'] = $npwpAttribute['atribut_value'];
                $data['attachment_npwp'] = $fileNpwpUrl;
            }

            if($bukuNikahAttribute) {
                $fileBukuNikahUrl = NULL;
                if ($bukuNikahAttribute['atribut_attachment'] && Storage::exists($bukuNikahAttribute['atribut_attachment'])) {
                    $fileBukuNikahUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $bukuNikahAttribute['atribut_attachment']] // File path parameter
                    );
                }
                $data['attr_buku_nikah'] = $bukuNikahAttribute['atribut_value'];
                $data['attachment_buku_nikah'] = $fileBukuNikahUrl;
            }

            return $this->sendResponse($data, 'Get Dokumen Profile Berhasil');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }

    }

    public function updateDoc(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'attr_no_ktp' => 'nullable',
                'attachment_ktp' => 'nullable|file|mimes:jpg,png|max:2048',
                'attr_no_kartu_pegawai' => 'nullable',
                'attachment_kartu_pegawai' => 'nullable|file|mimes:jpg,png|max:2048',
                'attr_no_kartu_keluarga' => 'nullable',
                'attachment_kartu_keluarga' => 'nullable|file|mimes:jpg,png|max:2048',
                'attr_npwp' => 'nullable',
                'attachment_npwp' => 'nullable|file|mimes:jpg,png|max:2048',
                'attr_buku_nikah' => 'nullable',
                'attachment_buku_nikah' => 'nullable|file|mimes:jpg,png|max:2048',
            ]);

            $user = $request->user();

            $p_anggota_id = $user->anggota?->p_anggota_id;

            if(!$p_anggota_id) {
                return response()->json(['message' => 'Tidak diizinkan update profile.'], 403);
            }

            // Check attribut
            $checkAtt = AnggotaAtributModels::where('p_anggota_id', $p_anggota_id)->get();

            $anggota = AnggotaModels::find($p_anggota_id);
            $checkUpdate = false;

            if($request->attr_no_ktp) {
                $anggota->ktp =  $request->attr_no_ktp;
                $checkUpdate = true;
            }

            if($request->attr_no_kartu_pegawai) {
                $anggota->nik =  $request->attr_no_kartu_pegawai;
                $checkUpdate = true;
            }

            if($checkUpdate) {
                $anggota->save();
            }

            if($request->file('attachment_ktp')) {
                $ktpPath = $request->file('attachment_ktp')->store('uploads/ktp', 'local');

                $ktpAttribute = $checkAtt->firstWhere('atribut_kode', 'ktp');

                if($ktpAttribute) {
                    AnggotaAtributModels::where([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'ktp'
                    ])
                    ->update([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_value' => $request->attr_no_ktp,
                        'atribut_attachment' => $ktpPath
                    ]);
                } else {
                    AnggotaAtributModels::create([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'ktp',
                        'atribut_value' => $request->attr_no_ktp,
                        'atribut_attachment' => $ktpPath
                    ]);
                }
            }

            if($request->file('attachment_kartu_pegawai')) {
                $kartuPegawaiPath = $request->file('attachment_kartu_pegawai')->store('uploads/kartu_pegawai', 'local');
                $kartuPegawaiAttribute = $checkAtt->firstWhere('atribut_kode', 'kartu_pegawai');

                if($kartuPegawaiAttribute) {
                    AnggotaAtributModels::where([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'kartu_pegawai'
                    ])
                    ->update([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_value' => $request->attr_no_kartu_pegawai,
                        'atribut_attachment' => $kartuPegawaiPath
                    ]);
                } else {
                    AnggotaAtributModels::create([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'kartu_pegawai',
                        'atribut_value' => $request->attr_no_kartu_pegawai,
                        'atribut_attachment' => $kartuPegawaiPath
                    ]);
                }
            }

            if($request->file('attachment_kartu_keluarga')) {
                $kkPath = $request->file('attachment_kartu_keluarga')->store('uploads/kartu_keluarga', 'local');
                $kkAttribute = $checkAtt->firstWhere('atribut_kode', 'kartu_keluarga');

                if($kkAttribute) {
                    AnggotaAtributModels::where([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'kartu_keluarga'
                    ])
                    ->update([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_value' => $request->attr_no_kartu_keluarga,
                        'atribut_attachment' => $kkPath
                    ]);
                } else {
                    AnggotaAtributModels::create([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'kartu_keluarga',
                        'atribut_value' => $request->attr_no_kartu_keluarga,
                        'atribut_attachment' => $kkPath
                    ]);
                }
            }

            if($request->file('attachment_npwp')) {
                $npwpPath = $request->file('attachment_npwp')->store('uploads/npwp', 'local');
                $npwpAttribute = $checkAtt->firstWhere('atribut_kode', 'npwp');

                if($npwpAttribute) {
                    AnggotaAtributModels::where([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'npwp'
                    ])
                    ->update([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_value' => $request->attr_npwp,
                        'atribut_attachment' => $npwpPath
                    ]);
                } else {
                    AnggotaAtributModels::create([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'npwp',
                        'atribut_value' => $request->attr_npwp,
                        'atribut_attachment' => $npwpPath
                    ]);
                }
            }

            if($request->file('attachment_buku_nikah')) {
                $bukuNikahPath = $request->file('attachment_buku_nikah')->store('uploads/buku_nikah', 'local');
                $bukuNikahAttribute = $checkAtt->firstWhere('atribut_kode', 'buku_nikah');

                if($bukuNikahAttribute) {
                    AnggotaAtributModels::where([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'buku_nikah'
                    ])
                    ->update([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_value' => $request->attr_npwp,
                        'atribut_attachment' => $bukuNikahPath
                    ]);
                } else {
                    AnggotaAtributModels::create([
                        'p_anggota_id' => $p_anggota_id,
                        'atribut_kode' => 'buku_nikah',
                        'atribut_value' => $request->attr_npwp,
                        'atribut_attachment' => $bukuNikahPath
                    ]);
                }
            }

            DB::commit();

            return $this->sendResponse([], 'Update Dokumen Profile Berhasil');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
