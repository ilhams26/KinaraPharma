<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\KategoriController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Login Admin/Staff
Route::post('/login-password', [AuthController::class, 'loginWithPassword']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/obats', [ObatController::class, 'index']);


Route::middleware('auth:api')->group(function () {

    // Auth 
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Detail Obat
    Route::get('/obats/{id}', [ObatController::class, 'show']);

    // Rute Transaksi Pembeli
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    // Rute Upload Resep Pembeli
    Route::prefix('prescriptions')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index']);
        Route::post('/', [PrescriptionController::class, 'store']);

        // Khusus Staff/Admin memvalidasi resep
        Route::post('/{id}/validate', [PrescriptionController::class, 'validatePrescription'])
            ->middleware('role:staff,admin');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/{id}', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });


    Route::middleware('role:staff,admin')->group(function () {

        // Update Status Pesanan Pembeli 
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        Route::post('/obats', [ObatController::class, 'store']);
        Route::post('/obats/{id}', [ObatController::class, 'update']);
        Route::delete('/obats/{id}', [ObatController::class, 'destroy']);
    });
});
