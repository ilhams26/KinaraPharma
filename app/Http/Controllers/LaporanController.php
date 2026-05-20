<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanExport;
use App\Exports\LaporanKeuanganExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // ==========================
    // LAPORAN STOK
    // ==========================
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->format('Y-m-d');

        // ==========================
        // PEMASUKAN STOK
        // ==========================
        $masuk = DB::table('batches')
            ->join('obat', 'batches.obat_id', '=', 'obat.id')
            ->whereBetween('batches.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'batches.created_at as tanggal',
                'batches.obat_id',
                'obat.nama',
                'batches.jumlah_awal',
                'batches.jumlah_sisa',
                DB::raw('0 as keluar')
            )
            ->get();

        // ==========================
        // PENGELUARAN STOK
        // ==========================
        $keluar = DB::table('pergerakan_stok')
            ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
            ->join('batches', 'pergerakan_stok.obat_id', '=', 'batches.obat_id')
            ->where('pergerakan_stok.tipe', 'keluar')
            ->whereBetween('pergerakan_stok.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'pergerakan_stok.created_at as tanggal',
                'pergerakan_stok.obat_id',
                'obat.nama',
                DB::raw('0 as jumlah_awal'),
                'batches.jumlah_sisa',
                'pergerakan_stok.jumlah as keluar'
            )
            ->get();

        // ==========================
        // GABUNG DATA
        // ==========================
        $dataGabung = $masuk
            ->merge($keluar)
            ->sortBy('tanggal')
            ->values();

        $data = [];

        foreach ($dataGabung as $i => $item) {

            $data[] = [
                'no' => $i + 1,
                'tanggal' => date('d-m-Y H:i', strtotime($item->tanggal)),
                'nama' => $item->nama,
                'jumlah_awal' => $item->jumlah_awal,
                'keluar' => $item->keluar,

                // 🔥 STOK REAL TERBARU
                'stok' => $item->jumlah_sisa,

                'stok_minimum' => 10
            ];
        }

        $totalMasuk = collect($data)->sum('jumlah_awal');
        $totalKeluar = collect($data)->sum('keluar');

        // 🔥 TOTAL STOK REAL
        $totalStok = DB::table('batches')->sum('jumlah_sisa');

        return view('laporan.index', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'totalStok' => $totalStok
        ]);
    }

    // ==========================
    // EXPORT EXCEL STOK
    // ==========================
    public function exportExcel(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->format('Y-m-d');

        $masuk = DB::table('batches')
            ->join('obat', 'batches.obat_id', '=', 'obat.id')
            ->whereBetween('batches.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'batches.created_at as tanggal',
                'batches.obat_id',
                'obat.nama',
                'batches.jumlah_awal',
                'batches.jumlah_sisa',
                DB::raw('0 as keluar')
            )
            ->get();

        $keluar = DB::table('pergerakan_stok')
            ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
            ->join('batches', 'pergerakan_stok.obat_id', '=', 'batches.obat_id')
            ->where('pergerakan_stok.tipe', 'keluar')
            ->whereBetween('pergerakan_stok.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'pergerakan_stok.created_at as tanggal',
                'pergerakan_stok.obat_id',
                'obat.nama',
                DB::raw('0 as jumlah_awal'),
                'batches.jumlah_sisa',
                'pergerakan_stok.jumlah as keluar'
            )
            ->get();

        $dataGabung = $masuk
            ->merge($keluar)
            ->sortBy('tanggal')
            ->values();

        $data = [];

        foreach ($dataGabung as $item) {

            $data[] = [
                'tanggal' => date('d-m-Y H:i', strtotime($item->tanggal)),
                'nama' => $item->nama,
                'jumlah_awal' => $item->jumlah_awal,
                'keluar' => $item->keluar,
                'stok' => $item->jumlah_sisa,
            ];
        }

        return Excel::download(new LaporanExport($data), 'laporan.xlsx');
    }

    // ==========================
    // EXPORT PDF STOK
    // ==========================
    public function exportPdf(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to ?? now()->format('Y-m-d');

        $masuk = DB::table('batches')
            ->join('obat', 'batches.obat_id', '=', 'obat.id')
            ->whereBetween('batches.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'obat.nama',
                'batches.jumlah_awal as pemasukan',
                DB::raw('0 as pengeluaran'),
                'batches.jumlah_sisa as stok_akhir'
            )
            ->get();

        $keluar = DB::table('pergerakan_stok')
            ->join('obat', 'pergerakan_stok.obat_id', '=', 'obat.id')
            ->join('batches', 'pergerakan_stok.obat_id', '=', 'batches.obat_id')
            ->where('pergerakan_stok.tipe', 'keluar')
            ->whereBetween('pergerakan_stok.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ])
            ->select(
                'obat.nama',
                DB::raw('0 as pemasukan'),
                'pergerakan_stok.jumlah as pengeluaran',
                'batches.jumlah_sisa as stok_akhir'
            )
            ->get();

        $data = $masuk
            ->merge($keluar)
            ->values()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama,
                    'pemasukan' => $item->pemasukan,
                    'pengeluaran' => $item->pengeluaran,
                    'stok_akhir' => $item->stok_akhir,
                ];
            });

        $pdf = Pdf::loadView('laporan.pdf', compact('data'));

        return $pdf->download('laporan.pdf');
    }

    // ==========================
    // LAPORAN KEUANGAN
    // ==========================
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
                'pergerakan_stok.obat_id',
                'obat.nama',
                'pergerakan_stok.jumlah',
                DB::raw('pergerakan_stok.jumlah * obat.harga as pemasukan')
            )
            ->orderBy('tanggal', 'asc')
            ->get();

        // 🔥 TAMBAH STOK REAL
        $data = $data->map(function ($item) {

            $stokReal = DB::table('batches')
                ->where('obat_id', $item->obat_id)
                ->sum('jumlah_sisa');

            $item->stok = $stokReal;

            return $item;
        });

        $totalMasuk = $data->sum('pemasukan');

        return view('laporan.keuangan', compact(
            'data',
            'from',
            'to',
            'totalMasuk'
        ));
    }

    // ==========================
// EXPORT EXCEL KEUANGAN
// ==========================
public function exportExcelKeuangan(Request $request)
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
            'pergerakan_stok.obat_id',
            'obat.nama',
            'pergerakan_stok.jumlah',
            DB::raw('pergerakan_stok.jumlah * obat.harga as pemasukan')
        )
        ->orderBy('tanggal', 'asc')
        ->get();

    // 🔥 TAMBAH STOK REAL
    $data = $data->map(function ($item) {

        $stokReal = DB::table('batches')
            ->where('obat_id', $item->obat_id)
            ->sum('jumlah_sisa');

        return [
            'tanggal'   => date('d-m-Y H:i', strtotime($item->tanggal)),
            'nama'      => $item->nama,
            'jumlah'    => $item->jumlah,
            'pemasukan' => $item->pemasukan,
            'stok'      => $stokReal
        ];
    });

    return Excel::download(
        new LaporanKeuanganExport($data),
        'laporan-keuangan.xlsx'
    );
}

   // ==========================
// EXPORT PDF KEUANGAN
// ==========================
public function exportPdfKeuangan(Request $request)
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
            'pergerakan_stok.obat_id',
            'obat.nama',
            'pergerakan_stok.jumlah',
            DB::raw('pergerakan_stok.jumlah * obat.harga as pemasukan')
        )
        ->orderBy('tanggal', 'asc')
        ->get();

    // 🔥 TAMBAH STOK REAL
    $data = $data->map(function ($item) {

        $stokReal = DB::table('batches')
            ->where('obat_id', $item->obat_id)
            ->sum('jumlah_sisa');

        $item->stok = $stokReal;

        return $item;
    });

    $totalMasuk = $data->sum('pemasukan');

    $pdf = Pdf::loadView('laporan.keuangan_pdf', compact(
        'data',
        'totalMasuk'
    ));

    return $pdf->download('laporan-keuangan.pdf');
}
