<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Obat;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        // 1. DATA CARD GLOBAL
        $totalObat = Obat::count();

        // Ambil obat yang stoknya <= stok minimum (Asumsi stok_total adalah kolom atau accessor yang udah ada)
        $obatMenipis = Obat::get()->filter(function ($obat) {
            return $obat->stok_total <= $obat->stok_minimum;
        });
        $obatMenipisCount = $obatMenipis->count();

        // 2. DATA KHUSUS ADMIN
        $pendapatanBulanIni = 0;
        $pesananSelesaiBulanIni = 0;
        $persenCash = 0;
        $persenCashless = 0;

        if ($role === 'admin') {
            $pendapatanBulanIni = Order::whereMonth('created_at', $bulanIni)
                ->whereYear('created_at', $tahunIni)
                ->where('payment_status', 'paid')
                ->sum('total_harga');

            $pesananSelesaiBulanIni = Order::whereMonth('created_at', $bulanIni)
                ->whereYear('created_at', $tahunIni)
                ->where('status', 'selesai')
                ->count();

            // Hitung Persentase Pie Chart
            $totalOrders = Order::where('payment_status', 'paid')->count();
            if ($totalOrders > 0) {
                $countCash = Order::where('metode_pembayaran', 'cash')->where('payment_status', 'paid')->count();
                $countMidtrans = Order::where('metode_pembayaran', 'midtrans')->where('payment_status', 'paid')->count();

                $persenCash = round(($countCash / $totalOrders) * 100);
                $persenCashless = round(($countMidtrans / $totalOrders) * 100);
            }
        }

        // 3. DATA KHUSUS STAFF
        $antreanPesanan = 0;
        $obatMasuk = 0;
        $notifKadaluarsa = [];
        $notifMenipis = $obatMenipis;

        if ($role === 'staff' || $role === 'admin') {
            $antreanPesanan = Order::whereIn('status', ['diproses'])->count();
            $obatMasuk = Batch::whereMonth('created_at', $bulanIni)->whereYear('created_at', $tahunIni)->sum('jumlah_awal');

            // Cari obat yang expired < 30 hari
            $notifKadaluarsa = DB::table('batches')
                ->join('obat', 'batches.obat_id', '=', 'obat.id')
                ->where('expired_date', '<=', Carbon::now()->addDays(30))
                ->where('expired_date', '>=', Carbon::now())
                ->select('obat.nama', 'batches.expired_date')
                ->get()
                ->map(function ($item) {
                    $item->sisa_hari = Carbon::parse($item->expired_date)->diffInDays(Carbon::now());
                    return $item;
                });
        }

        return view('dashboard', compact(
            'role',
            'pendapatanBulanIni',
            'totalObat',
            'pesananSelesaiBulanIni',
            'obatMenipisCount',
            'antreanPesanan',
            'obatMasuk',
            'persenCash',
            'persenCashless',
            'notifKadaluarsa',
            'notifMenipis'
        ));
    }

    // FUNGSI INI KHUSUS UNTUK MEMBACA FILTER TANGGAL DARI GRAFIK JAVASCRIPT
    public function getChartData(Request $request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end)->endOfDay();

        // Ambil data pendapatan per hari dalam rentang tanggal
        $sales = Order::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_harga) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $labels = [];
        $data = [];

        $currentDate = $start->copy();
        while ($currentDate->lte($end)) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M'); 

            $sale = $sales->firstWhere('date', $dateString);
            $data[] = $sale ? $sale->total : 0;

            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
