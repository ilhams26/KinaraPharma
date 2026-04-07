<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // Menampilkan Halaman Kasir
    public function index()
    {
        // Ambil data obat yang stoknya > 0
        $obats = Obat::whereHas('batches', function ($query) {
            $query->where('jumlah_sisa', '>', 0);
        })->get();

        return view('staff.kasir.index', compact('obats'));
    }

    // Memproses Pembayaran & Memotong Stok FEFO
    public function checkout(Request $request, OrderService $orderService)
    {
        $request->validate([
            'items' => 'required|string', // Berupa JSON dari frontend
            'pembayaran' => 'required|numeric',
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        try {
            // Panggil algoritma pemotongan stok otomatis (FEFO) dari OrderService
            // Kita gunakan ID user null (atau id staff) karena ini pembeli walk-in
            $order = $orderService->createOrder(
                null,
                $items,
                'cash'
            );

            return redirect()->back()->with('success', 'Pembayaran berhasil! Kembalian: Rp ' . number_format($request->pembayaran - $order->total_harga, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}
