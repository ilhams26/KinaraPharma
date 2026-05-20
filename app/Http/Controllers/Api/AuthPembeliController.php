<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthPembeliController extends Controller
{
    // Request OTP
    public function requestOtp(Request $request)
    {
        $request->validate(['no_hp' => 'required']);

        // 1. Cari atau Buat User
        $user = User::firstOrCreate(
            ['no_hp' => $request->no_hp],
            [
                'role' => 'pembeli',
                'username' => 'User_' . rand(1000, 9999),
                'password' => bcrypt('password123')
            ]
        );

        User::where('no_hp', $request->no_hp)->update(['otp' => '1234']);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim'
        ]);
    }
    // Verifikasi OTP Login
    public function loginPembeli(Request $request)
    {
        $request->validate(['no_hp' => 'required', 'otp' => 'required']);

        $user = User::where('no_hp', $request->no_hp)
            ->where('otp', $request->otp)
            ->where('role', 'pembeli')
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'OTP Salah!'], 401);
        }

        User::where('id', $user->id)->update(['otp' => null]);

        // 4. Generate Token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json(['success' => true, 'token' => $token, 'user' => $user]);
    }
}
