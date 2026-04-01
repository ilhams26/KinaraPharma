<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    // 1. Lihat Riwayat Pesanan (Untuk Pembeli)
    public function index()
    {
        $orders = Order::with('orderItems.obat')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function checkout(Request $request)
    {

        $request->validate([
            'items' => 'required|array',
            'items.*.obat_id' => 'required|exists:obat,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric',
            'payment_method' => 'required|in:midtrans,cash',
        ]);

        try {
            $order = $this->orderService->createOrder(
                auth()->id(),
                $request->items,
                $request->payment_method
            );

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load('orderItems.obat'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 3. Update Status Pesanan (KHUSUS STAFF/ADMIN)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,siap_diambil,selesai,dibatalkan',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // Kirim notifikasi otomatis jika pesanan siap diambil
        if ($request->status === 'siap_diambil') {
            $this->notificationService->sendOrderReady($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diupdate',
            'data' => $order,
        ]);
    }
}
