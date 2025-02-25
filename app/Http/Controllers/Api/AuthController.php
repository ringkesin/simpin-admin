<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('simpin')->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);

        // $client = \Laravel\Passport\Client::where('password_client', true)->first();

        // if (!$client) {
        //     return response()->json(['error' => 'OAuth client not found'], 500);
        // }
        // // $url = config('app.url');
        // $url = '127.0.0.1:8000';
        // // return $url;

        // $response = Http::asForm()->post($url . '/oauth/token', [
        //     'grant_type' => 'password',
        //     'client_id' => $client->id,
        //     'client_secret' => $client->secret,
        //     'username' => $request->email,
        //     'password' => $request->password,
        //     'scope' => '',
        // ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
