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
            'foto' => 'nullable|image|max:2048', // Validasi foto maksimal 2MB
        ]);

        $dataObat = [
            'nama' => $request->nama,
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'stok_minimum' => 10,
            'harga' => $request->harga,
            'deskripsi' => 'Obat ' . $request->nama,
        ];

        // LOGIKA UPLOAD FOTO
        if ($request->hasFile('foto')) {
            $dataObat['foto'] = $request->file('foto')->store('obat_images', 'public');
        }

        $obat = Obat::create($dataObat);

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
            'harga' => 'sometimes|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $dataObat = $request->except(['_token', '_method']);

        // LOGIKA UPDATE FOTO
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($obat->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($obat->foto);
            }
            $dataObat['foto'] = $request->file('foto')->store('obat_images', 'public');
        }

        $obat->update($dataObat);
        return redirect()->back()->with('success', 'Data obat berhasil diperbarui!');
    }
}
