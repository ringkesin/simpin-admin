<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    // **API Login**
    public function apiLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string', 
            'password' => 'required',
        ]);
    
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized', ['error' => 'The provided credentials are incorrect.'], 401);
        }   

        //check role untuk mendefine state = anggota / admin
        $token = $user->createToken('api-token',['state:anggota'])->plainTextToken;

        return $this->sendResponse(
            ['token' => $token, 'user' => $user], 
            'Login successful.'
        );
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
}
