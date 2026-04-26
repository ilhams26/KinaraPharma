<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalObat = Obat::count();
        $stokMenipis = Obat::where('stok_minimum', '>=', 10)->count();
        $resepMenunggu = Prescription::where('status', 'menunggu')->count();

        $pendapatanBulanIni = 15450000;

        return view('dashboard', compact('user', 'totalObat', 'stokMenipis', 'resepMenunggu', 'pendapatanBulanIni'));
    }
}
