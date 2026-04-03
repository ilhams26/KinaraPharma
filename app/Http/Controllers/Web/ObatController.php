<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObatController extends Controller
{
    // Halaman Data Obat (Read-Only untuk Admin)
    public function indexAdmin()
    {
        // Ambil data obat beserta relasi kategori dan batch stoknya
        $obats = Obat::with(['kategori', 'batches'])->orderBy('nama', 'asc')->get();
        return view('obat.index', compact('obats'));
    }

    // Halaman Kelola Obat (Full CRUD untuk Staff)
    public function indexStaff()
    {
        $obats = Obat::with(['kategori', 'batches'])->orderBy('nama', 'asc')->get();
        return view('staff.obat.index', compact('obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'jenis' => 'required|in:biasa,keras',
            'stok_awal' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'expired_date' => 'required|date',
        ]);

        // 1. Buat Data Obat
        $obat = Obat::create([
            'nama' => $request->nama,
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'stok_minimum' => 10, // Default minimum stok
            'harga' => $request->harga,
            'deskripsi' => 'Obat ' . $request->nama,
        ]);

        // 2. Buat Data Batch (Stok Awal)
        \App\Models\Batch::create([
            'obat_id' => $obat->id,
            'batch_number' => 'BATCH-' . strtoupper(\Illuminate\Support\Str::random(5)),
            'expired_date' => $request->expired_date,
            'jumlah_awal' => $request->stok_awal,
            'jumlah_sisa' => $request->stok_awal,
        ]);

        return redirect()->back()->with('success', 'Obat berhasil ditambahkan!');
    }

    // Menghapus Obat
    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);

        $obat->batches()->delete();
        $obat->delete();

        return redirect()->back()->with('success', 'Obat berhasil dihapus!');
    }
    // Update Data Obat (Khusus Staff)
    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'kategori_id' => 'sometimes|exists:kategori,id',
            'jenis' => 'sometimes|in:biasa,keras',
            'harga' => 'sometimes|numeric|min:0',
        ]);

        try {
            $obat->update($request->all());

            return redirect()->back()->with('success', 'Data obat berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui obat: ' . $e->getMessage());
        }
    }
}
