<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
// use App\Models\Order; 
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $orders = collect();

        $prescriptions = Prescription::with(['user', 'obat'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('staff.pesanan.index', compact('orders', 'prescriptions'));
    }
}
