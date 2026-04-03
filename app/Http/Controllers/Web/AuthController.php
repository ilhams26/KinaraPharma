<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Login
    public function showLoginForm()
    {
        // Kalau sudah login, langsung tendang ke dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    // 2. Memproses Data Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required', // Bisa ganti no_hp kalau mau
            'password' => 'required'
        ]);

        // Coba cocokan dengan database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek apakah yang login adalah Admin atau Staff
            $role = Auth::user()->role;
            if ($role === 'admin' || $role === 'staff') {
                return redirect()->intended('/dashboard');
            } else {
                // Kalau pembeli iseng login lewat web, keluarkan lagi
                Auth::logout();
                return back()->with('error', 'Akses ditolak! Halaman ini khusus Admin & Staff.');
            }
        }

        // Kalau password salah
        return back()->with('error', 'Username atau Password salah!');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
