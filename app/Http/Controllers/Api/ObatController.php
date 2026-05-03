<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Prescription;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with('kategori');

        // Filter by kategori
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Fitur Pencarian
        if ($request->has('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $obats = $query->get();

        return response()->json([
            'success' => true,
            'data' => $obats,
        ]);
    }

    public function show($id)
    {
        $obat = Obat::with('kategori')->findOrFail($id);

        $hasValidPrescription = false;

        if (auth()->check() && $obat->isObatKeras()) {
            $hasValidPrescription = Prescription::where('user_id', auth()->id())
                ->where('obat_id', $id)
                ->where('status', 'tervalidasi')
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data' => $obat,
            'has_valid_prescription' => $hasValidPrescription,
        ]);
    }
    // 3. Tambah Obat 
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'jenis' => 'required|in:biasa,keras',
            'stok_minimum' => 'required|integer|min:1',
            'foto' => 'nullable|image|max:2048', // Maksimal 2MB
        ]);

        try {
            $data = $request->except('foto');

            // Handle Upload Foto Obat
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('obat_images', 'public');
            }

            $obat = Obat::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data obat berhasil ditambahkan',
                'data' => $obat
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah obat: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4.(STAFF)
    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'kategori_id' => 'sometimes|exists:kategori,id',
            'nama' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'harga' => 'sometimes|numeric|min:0',
            'jenis' => 'sometimes|in:biasa,keras',
            'stok_minimum' => 'sometimes|integer|min:1',
            'foto' => 'nullable|image|max:2048',
        ]);

        try {
            $data = $request->except('foto');


            if ($request->hasFile('foto')) {
                if ($obat->foto) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($obat->foto);
                }
                $data['foto'] = $request->file('foto')->store('obat_images', 'public');
            }

            $obat->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data obat berhasil diperbarui',
                'data' => $obat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui obat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);

        try {
            if ($obat->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($obat->foto);
            }

            $obat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data obat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus obat' . $e->getMessage()
            ], 500);
        }
    }
}
