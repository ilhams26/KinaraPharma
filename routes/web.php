<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

// --- Rute Bebas (Publik) ---
Route::get('/', function () {
    return redirect('/login');
});

// Rute Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- Rute Wajib Login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Rute Obat Admin (Read-Only)
    Route::get('/data-obat', [\App\Http\Controllers\Web\ObatController::class, 'indexAdmin'])->name('admin.obat');

    // Rute Kelola Obat Staff (CRUD)
    Route::get('/kelola-obat', [\App\Http\Controllers\Web\ObatController::class, 'indexStaff'])->name('staff.obat');
});
