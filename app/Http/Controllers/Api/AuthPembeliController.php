<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthPembeliController extends Controller
{
    // Fungsi Request OTP
    public function requestOtp(Request $request)
    {
        $request->validate(['no_hp' => 'required']);

        $user = User::firstOrCreate(
            ['no_hp' => $request->no_hp],
            ['role' => 'pembeli', 'username' => 'User_' . rand(1000, 9999)]
        );

        $user->update(['otp' => '1234']);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim'
        ]);
    }

    // Fungsi Verifikasi OTP Login
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

        $user->update(['otp' => null]);
        $token = JWTAuth::fromUser($user);

        return response()->json(['success' => true, 'token' => $token, 'user' => $user]);
    }
}
