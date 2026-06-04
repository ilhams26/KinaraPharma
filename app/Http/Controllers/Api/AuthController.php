<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    // REGISTER 
    public function registerPembeli(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'no_hp' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }

    // LOGIN PEMBELI 
    public function loginPembeli(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|exists:users,no_hp',
            'password' => 'required',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Nomor HP atau Password salah'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // LOGIN STAFF/ADMIN 
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|exists:users,no_hp',
            'password' => 'required',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // GET PROFILE
    public function me()
    {
        return response()->json(['success' => true, 'user' => auth()->user()]);
    }

    // LOGOUT
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    // UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $user->update([
            'username' => $request->username,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user
        ], 200);
    }

    // FITUR OTP & LUPA PASSWORD

    public function sendOtp(Request $request)
    {
        $request->validate(['no_hp' => 'required|exists:users,no_hp']);
        $user = User::where('no_hp', $request->no_hp)->first();

        // WA Fonnte + random HAPUS '123456' nya.
        $this->otpService->generateAndSend($user, '123456');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|exists:users,no_hp',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$this->otpService->verify($user, $request->otp)) {
            return response()->json(['success' => false, 'message' => 'OTP Salah atau Kadaluarsa!'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $this->otpService->markAsVerified($user);

        return response()->json(['success' => true, 'message' => 'Password berhasil direset!']);
    }

    //  GANTI PASSWORD DARI  PROFIL

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama salah!'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah!'
        ], 200);
    }
}
