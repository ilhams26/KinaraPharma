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
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:obat,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            $orderId = 'INV-' . time() . '-' . strtoupper(Str::random(5));
            $grossAmount = 0;
            $itemDetails = [];

            foreach ($request->items as $item) {
                $obat = Obat::findOrFail($item['id']);
                $subtotal = $obat->harga * $item['quantity'];
                $grossAmount += $subtotal;

                $itemDetails[] = [
                    'id' => $obat->id,
                    'price' => (int) $obat->harga,
                    'quantity' => (int) $item['quantity'],
                    'name' => substr($obat->nama, 0, 50)
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderId,
                'total_price' => $grossAmount,
                'status' => 'unpaid',
            ]);

            foreach ($request->items as $item) {
                $obat = Obat::find($item['id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'obat_id' => $obat->id,
                    'quantity' => $item['quantity'],
                    'price' => $obat->harga,
                ]);
            }

            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->no_hp ?? '080000000000',
                ],
                'item_details' => $itemDetails
            ];

            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'snap_token' => $snapToken
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}
