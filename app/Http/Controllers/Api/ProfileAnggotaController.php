<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
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
}
