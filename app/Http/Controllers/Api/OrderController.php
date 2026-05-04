<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Obat;
use App\Services\OrderService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderService;
    protected $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with(['orderItems.obat'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat pesanan',
            'data' => $orders
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::with(['orderItems.obat'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan milik Anda'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil detail pesanan',
            'data' => $order
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,siap_diambil,selesai,dibatalkan',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        if ($request->status === 'siap_diambil') {
            $this->notificationService->sendOrderReady($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diupdate',
            'data' => $order,
        ]);
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

            foreach ($request->items as $item) {
                $obat = Obat::lockForUpdate()->findOrFail($item['obat_id']);

                if ($obat->stok_total < $item['qty']) {
                    throw new \Exception("Stok {$obat->nama} tidak mencukupi.");
                }

                $grossAmount += ($obat->harga * $item['qty']);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'metode_pembayaran' => 'cash',
                'total_harga' => $grossAmount,
                'status' => 'diproses',
                'payment_status' => 'unpaid',
            ]);

            foreach ($request->items as $item) {
                $obat = Obat::find($item['obat_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'obat_id' => $obat->id,
                    'harga' => $obat->harga,
                    'qty' => $item['qty'],
                    'subtotal' => $obat->harga * $item['qty'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan Cash berhasil dibuat',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($order->status !== 'diproses') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan karena sudah diproses atau selesai'
            ], 400);
        }

        $order->update([
            'status' => 'dibatalkan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan',
            'data' => $order
        ]);
    }
}
