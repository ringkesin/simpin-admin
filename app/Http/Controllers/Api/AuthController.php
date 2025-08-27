<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Main\DeleteAccountRequestModels;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthController extends BaseController
{
    // **API Login**
    public function apiLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::with(['roleUser', 'roleUser.role', 'anggota'])
            ->where('username', $request->username)
            ->whereHas('roleUser.role', function ($query) {
                $query->whereIn('code', ['mobile_anggota', 'mobile_admin']);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized', ['error' => 'The provided credentials are incorrect.'], 401);
        }

        $today = Carbon::today();

        //users validity
        if ($user->valid_from && $user->valid_from->greaterThan($today)) {
            return $this->sendError('Unauthorized', ['error' => 'Akun anda belum aktif'], 403);
        }
        if ($user->valid_until && $user->valid_until->lessThan($today)) {
            return $this->sendError('Unauthorized', ['error' => 'Akun anda sudah expired.'], 403);
        }

        $error = 0;
        $roleCode = null;
        foreach($user->roleUser as $r){
            if($r->role->apps->code == 'mobile'){
                $roleCode = $r->role->code;
                if ($r->valid_from && $r->valid_from->greaterThan($today)) {
                    $error++;
                }
                if ($r->valid_until && $r->valid_until->lessThan($today)) {
                    $error++;
                }
                if ($r->role->valid_from && $r->role->valid_from->greaterThan($today)) {
                    $error++;
                }
                if ($r->role->valid_until && $r->role->valid_until->lessThan($today)) {
                    $error++;
                }
            }
        }

        if( ! empty($user->anggota)){
            if ($user->anggota->valid_from && $user->anggota->valid_from->greaterThan($today)) {
                $error++;
            }
            if ($user->anggota->valid_to && $user->anggota->valid_to->lessThan($today)) {
                $error++;
            }
        }

        if($error == 0){
            $state = 'anggota';
            if($roleCode == 'mobile_admin'){
                $state = 'admin';
            }
            $token = $user->createToken('api-token',['state:'.$state])->plainTextToken;

            $user->makeHidden([
                'email_verified_at',
                'two_factor_confirmed_at',
                'current_team_id',
                'valid_from',
                'valid_until',
                'remarks',
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
                'roleUser',
                'anggota',
            ]);

            if( ! empty($user->anggota)){
                $user->anggota->makeHidden([
                    'valid_from',
                    'valid_to',
                    'p_jenis_kelamin_id',
                    'user_id',
                    'p_company_id',
                    'p_unit_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                    'is_registered'
                ]);
            }

            $overwriteUser = $user->toArray();
            $overwriteUser['profile_photo_url'] = \Illuminate\Support\Facades\Storage::disk('kkba_simpin')->temporaryUrl($user->profile_photo_path, now()->addMinutes(5));
            //$overwriteUser['profile_photo_url'] = \Illuminate\Support\Facades\Storage::disk('kkba_simpin')->temporaryUrl($user->profile_photo_path, now()->addMinutes(60));

            return $this->sendResponse(
                ['token' => $token, 'role' => $roleCode, 'anggota'=> $user->anggota, 'user' => $overwriteUser, ],
                'Login successful.'
            );
        }

        return $this->sendError('Unauthorized', ['error' => 'Anda tidak memiliki akses.'], 401);
    }

    // **API Logout**
    public function apiLogout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse([], 'Successfully logged out.');
    }

    // **API Logout**
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect', [], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->sendResponse([], 'Password changed successfully');
    }

    public function requestDeleteAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomor_anggota' => 'required',
                'email' => 'required',
                'no_robots' => 'required',
                'remarks' => 'required'
            ],[
                'nomor_anggota.required' => 'No Anggota harus diisi',
                'email.required' => 'Email harus diisi',
                'no_robots.required' => 'Validasi text harus diisi',
                'remarks.required' => 'Keterangan harus diisi'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            if($request->no_robots != 'delete_account')
            {
                return $this->sendError('Validasi Text salah', [], 400);
            }

            DB::beginTransaction();

            $user = $request->user();
            $isAnggota = $user->tokenCan('state:anggota');
            $anggota = $user->anggota;

            if(!$isAnggota) {
                return $this->sendError('Maaf anda tidak terdaftar sebagai anggota', [], 400);
            }

            if($anggota->nomor_anggota != $request->nomor_anggota)
            {
                return $this->sendError('Nomor anggota tidak sesuai', [], 400);
            }

            if($anggota->email != $request->email)
            {
                return $this->sendError('Email tidak sesuai', [], 400);
            }

            $payloadInsert = [
                'user_id' => $user->id,
                'remarks' => $request->remarks,
                'status' => 'open',
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            $processInsert = DeleteAccountRequestModels::create($payloadInsert);

            DB::commit();

            return $this->sendResponse($processInsert, 'Request tutup akun dikirim ke admin');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error : ' . $e->getMessage());
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
