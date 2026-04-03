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

    // Kelola Obat Staff (CRUD)
    Route::prefix('kelola-obat')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ObatController::class, 'indexStaff'])->name('staff.obat');
        Route::post('/', [\App\Http\Controllers\Web\ObatController::class, 'store'])->name('staff.obat.store');
        // RUTE UPDATE BARU
        Route::put('/{id}', [\App\Http\Controllers\Web\ObatController::class, 'update'])->name('staff.obat.update');
        Route::delete('/{id}', [\App\Http\Controllers\Web\ObatController::class, 'destroy'])->name('staff.obat.destroy');
    });
});
