<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StokController extends Controller
{
    public function index()
    {
        $obats = Obat::with(['batches' => function ($query) {
            $query->orderBy('expired_date', 'asc');
        }])->orderBy('nama', 'asc')->get();

        return view('staff.stok.index', compact('obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required',
            'jumlah' => 'required|integer|min:1',
            'expired_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $batch = Batch::create([
                'obat_id' => $request->obat_id,
                'batch_number' => 'BATCH-' . strtoupper(Str::random(5)),
                'expired_date' => $request->expired_date,
                'jumlah_awal' => $request->jumlah,
                'jumlah_sisa' => $request->jumlah,
            ]);

            DB::table('pergerakan_stok')->insert([
                'obat_id' => $request->obat_id,
                'tipe' => 'masuk',
                'jumlah' => $request->jumlah,
                'sumber' => 'Supplier / Barang Baru',
                'referensi_id' => $batch->id,
                'keterangan' => 'Penambahan batch baru',
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Stok Batch baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah stok: ' . $e->getMessage());
        }
    }

    //Penyesuaian Stok
    public function adjust(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'tipe_penyesuaian' => 'required|in:keluar,masuk',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $batch = Batch::findOrFail($request->batch_id);

            // Logika Matematika
            if ($request->tipe_penyesuaian === 'keluar') {
                if ($request->jumlah > $batch->jumlah_sisa) {
                    return redirect()->back()->with('error', 'Jumlah penyesuaian melebihi sisa stok batch ini!');
                }
                $batch->jumlah_sisa -= $request->jumlah;
            } else {

                $batch->jumlah_sisa += $request->jumlah;
            }
            $batch->save();

            DB::table('pergerakan_stok')->insert([
                'obat_id' => $batch->obat_id,
                'tipe' => $request->tipe_penyesuaian,
                'jumlah' => $request->jumlah,
                'sumber' => 'Penyesuaian Manual',
                'referensi_id' => $batch->id,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Stok berhasil disesuaikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }
}
