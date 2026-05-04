<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // =========================================
    // LAPORAN STOK (TIDAK DIUBAH)
    // =========================================
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->format('Y-m-d');

        $masuk = DB::table('batches')
            ->join('obat', 'batches.obat_id', '=', 'obat.id')
            ->whereBetween('batches.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(
                'batches.created_at as tanggal',
                'batches.obat_id',
                'obat.nama',
                'batches.jumlah_awal',
                DB::raw('0 as keluar')
            )
            ->get();

        $keluar = DB::table('pergerakan_stok')
            ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
            ->whereRaw('LOWER(pergerakan_stok.tipe) = "keluar"') // 🔥 FIX
            ->whereBetween('pergerakan_stok.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(
                'pergerakan_stok.created_at as tanggal',
                'pergerakan_stok.obat_id',
                'obat.nama',
                DB::raw('0 as jumlah_awal'),
                'pergerakan_stok.jumlah as keluar'
            )
            ->get();

        $dataGabung = $masuk->merge($keluar)->sortBy('tanggal')->values();

        $stok = [];
        $data = [];

        foreach ($dataGabung as $i => $item) {

            if (!isset($stok[$item->obat_id])) {
                $stok[$item->obat_id] = 0;
            }

            if ($item->jumlah_awal > 0) {
                $stok[$item->obat_id] += $item->jumlah_awal;
            }

            if ($item->keluar > 0) {
                $stok[$item->obat_id] -= $item->keluar;
            }

            $data[] = [
                'no' => $i + 1,
                'tanggal' => date('d-m-Y H:i', strtotime($item->tanggal)),
                'nama' => $item->nama,
                'jumlah_awal' => $item->jumlah_awal,
                'keluar' => $item->keluar,
                'stok' => $stok[$item->obat_id],
                'stok_minimum' => 10
            ];
        }

        $totalMasuk = collect($data)->sum('jumlah_awal');
        $totalKeluar = collect($data)->sum('keluar');
        $totalStok = DB::table('batches')->sum('jumlah_sisa');

        return view('laporan.index', compact(
            'data','from','to','totalMasuk','totalKeluar','totalStok'
        ));
    }

    // =========================================
    // EXPORT EXCEL STOK
    // =========================================
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanExport($this->getData($request)),
            'laporan.xlsx'
        );
    }

    // =========================================
    // EXPORT PDF STOK
    // =========================================
    public function exportPdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = Pdf::loadView('laporan.pdf', compact('data'));
        return $pdf->download('laporan.pdf');
    }

    // =========================================
    // 🔥 FUNCTION AMBIL DATA (BIAR RAPI)
    // =========================================
    private function getData($request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->format('Y-m-d');

        $masuk = DB::table('batches')
            ->join('obat', 'batches.obat_id', '=', 'obat.id')
            ->whereBetween('batches.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(
                'batches.created_at as tanggal',
                'batches.obat_id',
                'obat.nama',
                'batches.jumlah_awal',
                DB::raw('0 as keluar')
            )
            ->get();

        $keluar = DB::table('pergerakan_stok')
            ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
            ->whereRaw('LOWER(pergerakan_stok.tipe) = "keluar"')
            ->whereBetween('pergerakan_stok.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(
                'pergerakan_stok.created_at as tanggal',
                'pergerakan_stok.obat_id',
                'obat.nama',
                DB::raw('0 as jumlah_awal'),
                'pergerakan_stok.jumlah as keluar'
            )
            ->get();

        $dataGabung = $masuk->merge($keluar)->sortBy('tanggal')->values();

        $stok = [];
        $data = [];

        foreach ($dataGabung as $item) {

            if (!isset($stok[$item->obat_id])) {
                $stok[$item->obat_id] = 0;
            }

            if ($item->jumlah_awal > 0) {
                $stok[$item->obat_id] += $item->jumlah_awal;
            }

            if ($item->keluar > 0) {
                $stok[$item->obat_id] -= $item->keluar;
            }

            $data[] = [
                'nama' => $item->nama,
                'jumlah_awal' => $item->jumlah_awal,
                'keluar' => $item->keluar,
                'stok' => $stok[$item->obat_id],
            ];
        }

        return $data;
    }

    // =========================================
    // 💰 LAPORAN KEUANGAN (BARU)
    // =========================================
    public function keuangan(Request $request)
{
    $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
    $to   = $request->to ?? now()->format('Y-m-d');

    $data = DB::table('pergerakan_stok')
        ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
        ->where('pergerakan_stok.tipe', 'keluar')
        ->whereBetween('pergerakan_stok.created_at', [
            $from . ' 00:00:00',
            $to . ' 23:59:59'
        ])
        ->select(
            'pergerakan_stok.created_at as tanggal',
            'obat.nama',
            'pergerakan_stok.jumlah',
            DB::raw('pergerakan_stok.jumlah * obat.harga as pemasukan')
        )
        ->orderBy('tanggal', 'asc')
        ->get();

    $totalMasuk = $data->sum('pemasukan');

    return view('laporan.keuangan', compact(
        'data',
        'from',
        'to',
        'totalMasuk'
    ));
}
    // =========================================
    // EXPORT EXCEL KEUANGAN
    // =========================================
    public function exportExcelKeuangan(Request $request)
    {
        $data = $this->keuangan($request)->getData()['data'];

        return Excel::download(new LaporanExport($data), 'laporan-keuangan.xlsx');
    }

    // =========================================
    // EXPORT PDF KEUANGAN
    // =========================================
    public function exportPdfKeuangan(Request $request)
    {
        $data = $this->keuangan($request)->getData()['data'];

        $pdf = Pdf::loadView('laporan.pdf_keuangan', compact('data'));

        return $pdf->download('laporan-keuangan.pdf');
    }
}