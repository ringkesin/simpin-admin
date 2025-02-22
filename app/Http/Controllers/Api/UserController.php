<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use App\Traits\MyResponse;

class UserController extends Controller
{
    use MyResponse;

    public function changePass(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        $user = $request->user();

        // Cek apakah password lama benar
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Password lama salah'], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->response(TRUE, 200, 'Password Changed', []);

    }
}
