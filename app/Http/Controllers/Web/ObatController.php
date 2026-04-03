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
}