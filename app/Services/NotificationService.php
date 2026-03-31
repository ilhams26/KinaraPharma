<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Prescription;

class NotificationService
{
    public function sendOrderReady(Order $order)
    {
        if (!$order->user_id) return; 

        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Pesanan Siap Diambil!',
            'message' => "Pesanan {$order->order_code} sudah siap diambil.",
            'tipe' => 'order',
            'reference_id' => $order->id,
        ]);
    }

    public function sendPrescriptionValidated(Prescription $prescription)
    {
        Notification::create([
            'user_id' => $prescription->user_id,
            'title' => 'Resep Divalidasi',
            'message' => "Resep untuk {$prescription->obat->nama} telah divalidasi. Anda sekarang bisa checkout.",
            'tipe' => 'resep',
            'reference_id' => $prescription->id,
        ]);
    }
}
