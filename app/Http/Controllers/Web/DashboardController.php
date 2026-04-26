<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // 🚨 Wajib ditambahkan untuk memanipulasi tanggal

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $semuaObat = Obat::with('batches')->get();
        $totalObat = $semuaObat->count();

        // 2. Hitung Obat Menipis
        $obatMenipis = $semuaObat->filter(function ($obat) {

            $stokReal = $obat->batches->sum('jumlah_sisa');
            return $stokReal <= $obat->stok_minimum;
        });

        $obatMenipisCount = $obatMenipis->count();

        if ($role === 'admin') {

            $pendapatanBulanIni = 15450000; 
            $pesananSelesaiBulanIni = 120; 

            $persenCash = 65;
            $persenCashless = 35;

            return view('dashboard', compact(
                'role',
                'totalObat',
                'obatMenipisCount',
                'pendapatanBulanIni',
                'pesananSelesaiBulanIni',
                'persenCash',
                'persenCashless'
            ));
        } else {

            $obatMasuk = 50; 
            $antreanPesanan = Prescription::where('status', 'menunggu')->count();

            $notifMenipis = $obatMenipis->take(5)->map(function ($obat) {
                $obat->stok_total = $obat->batches->sum('jumlah_sisa');
                return $obat;
            });

            $notifKadaluarsa = collect();
            $batasKadaluarsa = Carbon::now()->addDays(90); 

            foreach ($semuaObat as $obat) {

                $batchHampirExp = $obat->batches
                    ->where('jumlah_sisa', '>', 0)
                    ->where('expired_date', '<=', $batasKadaluarsa->toDateString())
                    ->sortBy('expired_date') 
                    ->first();

                if ($batchHampirExp) {
                    $sisaHari = Carbon::now()->diffInDays(Carbon::parse($batchHampirExp->expired_date), false);

                    $notifKadaluarsa->push((object)[
                        'nama' => $obat->nama,
                        'sisa_hari' => $sisaHari > 0 ? ceil($sisaHari) : 0
                    ]);
                }
            }

            $notifKadaluarsa = $notifKadaluarsa->take(5);

            return view('dashboard', compact(
                'role',
                'totalObat',
                'obatMenipisCount',
                'obatMasuk',
                'antreanPesanan',
                'notifMenipis',
                'notifKadaluarsa'
            ));
        }
    }
}
