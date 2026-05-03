<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function getSnapToken(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'TEST-' . uniqid();
        $grossAmount = 50000;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => 'Ilham',
                'last_name' => 'Ghazali',
                'email' => 'ilham@kinarapharma.com',
                'phone' => '081234567890',
            ],
            'item_details' => [
                [
                    'id' => 'OBT-001',
                    'price' => 50000,
                    'quantity' => 1,
                    'name' => 'Obat Test Integrasi'
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'message' => 'Koneksi Midtrans Sukses!',
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            $orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);
            $grossAmount = 0;
            $itemDetails = [];

            foreach ($request->items as $item) {
                $obat = Obat::lockForUpdate()->findOrFail($item['obat_id']);

                if ($obat->stok_total < $item['qty']) {
                    throw new \Exception("Stok {$obat->nama} tidak mencukupi. Sisa stok: {$obat->stok_total}");
                }

                $subtotal = $obat->harga * $item['qty'];
                $grossAmount += $subtotal;

                $itemDetails[] = [
                    'id' => $obat->id,
                    'price' => (int) $obat->harga,
                    'quantity' => (int) $item['qty'],
                    'name' => substr($obat->nama, 0, 50)
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'metode_pembayaran' => 'midtrans',
                'total_harga' => $grossAmount,
                'status' => 'diproses',
                'payment_status' => 'unpaid',
            ]);

            foreach ($request->items as $item) {
                $obat = Obat::find($item['obat_id']);
                $subtotal = $obat->harga * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'obat_id' => $obat->id,
                    'harga' => $obat->harga,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);
            }

            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderCode,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->username ?? 'Pelanggan',
                    'email' => $user->email,
                    'phone' => $user->no_hp ?? '080000000000',
                ],
                'item_details' => $itemDetails
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'order_code' => $orderCode,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    // public function notificationHandler(Request $request)
    // {
    //     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

    //     try {
    //         $notification = new \Midtrans\Notification();

    //         $transaction = $notification->transaction_status;
    //         $type = $notification->payment_type;
    //         $orderId = $notification->order_id;
    //         $fraud = $notification->fraud_status;

    //         $order = Order::where('order_code', $orderId)->first();

    //         if (!$order) {
    //             return response()->json(['message' => 'Order tidak ditemukan'], 404);
    //         }

    //         if ($transaction == 'capture') {
    //             if ($type == 'credit_card') {
    //                 if ($fraud == 'challenge') {
    //                     $order->update(['payment_status' => 'unpaid']);
    //                 } else {
    //                     $order->update(['payment_status' => 'paid']);
    //                 }
    //             }
    //         } else if ($transaction == 'settlement') {
    //             $order->update(['payment_status' => 'paid']);
    //         } else if ($transaction == 'pending') {
    //             $order->update(['payment_status' => 'unpaid']);
    //         } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
    //             $order->update(['payment_status' => 'unpaid', 'status' => 'dibatalkan']);
    //         }

    //         return response()->json(['message' => 'Notification handled successfully']);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }

    public function notificationHandler(Request $request)
    {
        $transaction = $request->transaction_status;
        $type = $request->payment_type;
        $orderId = $request->order_id;
        $fraud = $request->fraud_status;

        // Cari pesanan berdasarkan order_code
        $order = Order::where('order_code', $orderId)->first();

        // Jika tidak ketemu, balas 404
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan untuk ID: ' . $orderId], 404);
        }

        // Logika perubahan status
        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $order->update(['payment_status' => 'unpaid']);
                } else {
                    $order->update(['payment_status' => 'paid']);
                }
            }
        } else if ($transaction == 'settlement') {
            $order->update(['payment_status' => 'paid']);
        } else if ($transaction == 'pending') {
            $order->update(['payment_status' => 'unpaid']);
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $order->update(['payment_status' => 'unpaid', 'status' => 'dibatalkan']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification handled successfully. Status is now: ' . $order->payment_status
        ]);
    }
}
