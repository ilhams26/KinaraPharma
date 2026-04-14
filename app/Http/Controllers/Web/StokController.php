<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StokController extends Controller
{
    //  Halaman Kelola Stok
    public function index()
    {
        $obats = Obat::with('batches')->orderBy('nama', 'asc')->get();
        return view('staff.stok.index', compact('obats'));
    }

    // (Batch) Baru
    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required',
            'jumlah' => 'required|integer|min:1',
            'expired_date' => 'required|date',
        ]);

        Batch::create([
            'obat_id' => $request->obat_id,
            'batch_number' => 'BATCH-' . strtoupper(Str::random(5)),
            'expired_date' => $request->expired_date,
            'jumlah_awal' => $request->jumlah,
            'jumlah_sisa' => $request->jumlah,
        ]);

        return redirect()->back()->with('success', 'Stok Batch baru berhasil ditambahkan!');
    }
}
