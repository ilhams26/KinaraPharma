<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // Login
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $throttleKey = 'login-web-' . $request->ip() . ':' . $request->username;
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = max(1, ceil($seconds / 60));

            Log::warning('Security Alert: Brute force detected.', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'target_username' => $request->username
            ]);

            return back()->with('error', "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$minutes} menit.")
                ->with('lockout_time', $seconds)
                ->withInput($request->only('username'));
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            $role = Auth::user()->role;
 
            if ($role === 'admin' || $role === 'staff') {
                Log::info('Security Event: Successful Web Login.', [
                    'username' => Auth::user()->username,
                    'role' => $role,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return redirect()->intended('/dashboard');
            } else {
                Auth::logout();

                Log::notice('Security Event: Unauthorized Access Attempt (Pembeli to Web).', [
                    'username' => $request->username,
                    'ip_address' => $request->ip()
                ]);

                return back()->with('error', 'Akses ditolak! Halaman ini khusus Admin & Staff.');
            }
        }

        RateLimiter::hit($throttleKey, 60);

        // log gagal login
        Log::notice('Security Event: Failed Web Login.', [
            'attempted_username' => $request->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('error', 'Username atau Password salah!')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        $username = Auth::user() ? Auth::user()->username : 'Unknown';

        // log logout
        Log::info('Security Event: User Logged Out.', [
            'username' => $username,
            'ip_address' => $request->ip()
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
