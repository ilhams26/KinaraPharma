<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        // 1. Ambil data pesanan (Tabel Kiri)
        $orders = Order::with(['user', 'orderItems.obat'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. KEMBALIKAN QUERY YANG HILANG: Ambil data resep (Tabel Kanan)
        $prescriptions = Prescription::with(['user', 'obat'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('staff.pesanan.index', [
            'pesanans' => $orders,
            'prescriptions' => $prescriptions
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,siap_diambil,selesai,dibatalkan'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status pesanan ' . $order->order_code . ' berhasil diupdate!');
    }
}
