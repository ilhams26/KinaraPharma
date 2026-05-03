<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\AuthPembeliController;
use App\Http\Controllers\Api\PaymentController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Login Admin/Staff
Route::post('/login-password', [AuthController::class, 'loginWithPassword']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/obats', [ObatController::class, 'index']);

// Pembeli Mobile
Route::post('/request-otp', [AuthPembeliController::class, 'requestOtp']);
Route::post('/login-pembeli', [AuthPembeliController::class, 'loginPembeli']);

Route::post('/midtrans/test', [PaymentController::class, 'getSnapToken']);
Route::post('/midtrans/callback', [PaymentController::class, 'notificationHandler']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/obats/{id}', [ObatController::class, 'show']);

    Route::post('/midtrans/checkout', [PaymentController::class, 'checkout']);

    // Transaksi Pembeli
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    //  Upload Resep 
    Route::prefix('prescriptions')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index']);
        Route::post('/', [PrescriptionController::class, 'store']);
        Route::post('/upload', [PrescriptionController::class, 'upload']);

        // memvalidasi resep
        Route::post('/{id}/validate', [PrescriptionController::class, 'validatePrescription'])
            ->middleware('role:staff,admin');
    });

    // Notifikasi
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/{id}', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });

    //  Admin & Staff
    Route::middleware('role:staff,admin')->group(function () {

        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        Route::post('/obats', [ObatController::class, 'store']);
        Route::post('/obats/{id}', [ObatController::class, 'update']);
        Route::delete('/obats/{id}', [ObatController::class, 'destroy']);
    });
});
