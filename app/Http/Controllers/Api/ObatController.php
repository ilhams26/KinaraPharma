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
}
