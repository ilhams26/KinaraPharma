<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    private function tooManyAttemptsResponse(string $key, string $message = 'Terlalu banyak percobaan. Silakan coba lagi nanti.')
    {
        $seconds = RateLimiter::availableIn($key);
        $minutes = max(1, ceil($seconds / 60));

        return response()->json([
            'success' => false,
            'message' => $message,
            'retry_after_seconds' => $seconds,
            'retry_after_minutes' => $minutes,
        ], 429);
    }

    public function registerPembeli(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'no_hp' => 'required|string|max:20|unique:users,no_hp',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ]);

        Log::info('Security Event: Pembeli Registered.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'user' => $user,
        ], 201);
    }

    public function loginPembeli(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login-mobile-pembeli:' . $request->ip() . ':' . $request->no_hp;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            Log::warning('Security Alert: Mobile login brute force detected.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->tooManyAttemptsResponse(
                $throttleKey,
                'Terlalu banyak percobaan login gagal. Silakan coba lagi nanti.'
            );
        }

        $user = User::where('no_hp', $request->no_hp)
            ->where('role', 'pembeli')
            ->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            Log::notice('Security Event: Failed Mobile Pembeli Login.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nomor HP atau Password salah',
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        $token = JWTAuth::fromUser($user);

        Log::info('Security Event: Successful Mobile Pembeli Login.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login-api-staff-admin:' . $request->ip() . ':' . $request->no_hp;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            Log::warning('Security Alert: API staff/admin brute force detected.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->tooManyAttemptsResponse(
                $throttleKey,
                'Terlalu banyak percobaan login gagal. Silakan coba lagi nanti.'
            );
        }

        $user = User::where('no_hp', $request->no_hp)
            ->whereIn('role', ['staff', 'admin'])
            ->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            Log::notice('Security Event: Failed API Staff/Admin Login.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid',
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        $token = JWTAuth::fromUser($user);

        Log::info('Security Event: Successful API Staff/Admin Login.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'user' => auth()->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        Log::info('Security Event: API User Logged Out.', [
            'user_id' => $user?->id,
            'no_hp' => $user?->no_hp,
            'role' => $user?->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $user->update([
            'username' => $request->username,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        Log::info('Security Event: Profile Updated.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user,
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
        ]);

        $throttleKey = 'send-otp:' . $request->ip() . ':' . $request->no_hp;

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            Log::warning('Security Alert: OTP request limit exceeded.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->tooManyAttemptsResponse(
                $throttleKey,
                'Terlalu banyak permintaan OTP. Silakan coba lagi nanti.'
            );
        }

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 300);

            Log::notice('Security Event: OTP Request For Unknown Number.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak ditemukan',
            ], 404);
        }

        RateLimiter::hit($throttleKey, 300);

        // MODE DEMO:
        // OTP statis untuk demo/testing. Jika Fonnte sudah siap, ubah logic di OtpService.
        $this->otpService->generateAndSend($user, '123456');

        // MODE PRODUCTION CONTOH:
        // $this->otpService->generateAndSend($user);

        Log::info('Security Event: OTP Requested.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $throttleKey = 'reset-password:' . $request->ip() . ':' . $request->no_hp;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            Log::warning('Security Alert: Reset password OTP brute force detected.', [
                'no_hp' => $request->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->tooManyAttemptsResponse(
                $throttleKey,
                'Terlalu banyak percobaan reset password. Silakan coba lagi nanti.'
            );
        }

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 300);

            return response()->json([
                'success' => false,
                'message' => 'Nomor HP atau OTP tidak valid',
            ], 400);
        }

        if (!$this->otpService->verify($user, $request->otp)) {
            RateLimiter::hit($throttleKey, 300);

            Log::notice('Security Event: Failed Reset Password OTP Verification.', [
                'user_id' => $user->id,
                'no_hp' => $user->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OTP salah atau kadaluarsa!',
            ], 400);
        }

        RateLimiter::clear($throttleKey);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $this->otpService->markAsVerified($user);

        Log::info('Security Event: Password Reset Successfully.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset!',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:old_password',
        ]);

        $user = auth()->user();
        $throttleKey = 'change-password:' . $request->ip() . ':' . $user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return $this->tooManyAttemptsResponse(
                $throttleKey,
                'Terlalu banyak percobaan ganti password. Silakan coba lagi nanti.'
            );
        }

        if (!Hash::check($request->old_password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);

            Log::notice('Security Event: Failed Change Password.', [
                'user_id' => $user->id,
                'no_hp' => $user->no_hp,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Password lama salah!',
            ], 400);
        }

        RateLimiter::clear($throttleKey);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Log::info('Security Event: Password Changed Successfully.', [
            'user_id' => $user->id,
            'no_hp' => $user->no_hp,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah!',
        ], 200);
    }
}
