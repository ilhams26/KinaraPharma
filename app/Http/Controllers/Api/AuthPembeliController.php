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

        // 1. Cari atau Buat User
        $user = User::firstOrCreate(
            ['no_hp' => $request->no_hp],
            [
                'role' => 'pembeli',
                'username' => 'User_' . rand(1000, 9999),
                'password' => bcrypt('password123')
            ]
        );

        // 🚨 2. JALUR BYPASS: Tembak langsung ke MySQL tanpa peduli aturan Model
        User::where('no_hp', $request->no_hp)->update(['otp' => '1234']);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim'
        ]);
    }

    // Fungsi Verifikasi OTP Login
    public function loginPembeli(Request $request)
    {
        $request->validate(['no_hp' => 'required', 'otp' => 'required']);

        // 1. Cek kecocokan nomor HP, OTP, dan Role
        $user = User::where('no_hp', $request->no_hp)
            ->where('otp', $request->otp)
            ->where('role', 'pembeli')
            ->first();

        // 2. Jika tidak cocok, tolak!
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'OTP Salah!'], 401);
        }

        // 🚨 3. JALUR BYPASS: Hapus OTP setelah berhasil agar aman
        User::where('id', $user->id)->update(['otp' => null]);

        // 4. Generate Token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json(['success' => true, 'token' => $token, 'user' => $user]);
    }
}
