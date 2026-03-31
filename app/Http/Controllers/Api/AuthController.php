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
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'no_hp' => 'required|string|unique:users',
        ]);

        $otp = $this->otpService->generate();

        $user = User::create([
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'role' => 'pembeli',
        ]);

        $this->otpService->saveToUser($user, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp,
        ]);
    }

    // 2. Kirim OTP (Login Pembeli)
    public function sendOtp(Request $request)
    {
        $request->validate(['no_hp' => 'required|exists:users,no_hp']);

        $user = User::where('no_hp', $request->no_hp)->first();
        $otp = $this->otpService->generate();

        $this->otpService->saveToUser($user, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp,
        ]);
    }

    // 3. Verifikasi OTP (Masuk ke Aplikasi)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|exists:users,no_hp',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$this->otpService->verify($user, $request->otp)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 401);
        }

        $this->otpService->markAsVerified($user);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // 4. Login Khusus Admin / Staff 
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|exists:users,no_hp',
            'password' => 'required',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me()
    {
        return response()->json(['success' => true, 'user' => auth()->user()]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
