<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {

        $orders = Order::with(['user', 'orderItems.obat'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.pesanan.index', ['pesanans' => $orders]);
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
