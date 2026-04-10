<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Services\OrderService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $obats = Obat::whereHas('batches', function ($query) {
            $query->where('jumlah_sisa', '>', 0);
        })->get();

        return view('staff.kasir.index', compact('obats'));
    }

    public function checkout(Request $request, OrderService $orderService)
    {
        $request->validate([
            'items' => 'required|string',
            'pembayaran' => 'required|numeric',
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        try {
            $order = $orderService->createOrder(
                null,
                $items,
                'cash'
            );

            return redirect()->back()->with('success', 'Transaksi Berhasil! Kembalian: Rp ' . number_format($request->pembayaran - $order->total_harga, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}
