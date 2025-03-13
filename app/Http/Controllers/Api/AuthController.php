<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    // **API Registration**
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'token' => $user->createToken('api-token')->plainTextToken,
    //         'user' => $user
    //     ]);
    // }

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
        // return response()->json(['message' => 'Logged out successfully']);
        return $this->sendResponse([], 'Successfully logged out.');
    }

    // // **Web Login (Session-based)**
    // public function webLogin(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'username' => 'required|username',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('/dashboard'); // Redirect to dashboard
    //     }

    //     return back()->withErrors([
    //         'email' => 'Invalid credentials',
    //     ]);
    // }

    // // **Web Logout**
    // public function webLogout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/login');
    // }
}
