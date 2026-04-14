<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\LaporanExport;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $query = DB::table('pergerakan_stok')
            ->join('obat', 'obat.id', '=', 'pergerakan_stok.obat_id')
            ->select(
                'pergerakan_stok.created_at as tanggal',
                'obat.nama',
                'pergerakan_stok.tipe',
                'pergerakan_stok.jumlah',
                'obat.stok_minimum'
            )
            ->orderBy('pergerakan_stok.created_at', 'asc');

        if ($from && $to) {
            $query->whereBetween('pergerakan_stok.created_at', [$from, $to]);
        }

        $rows = $query->get();

        $data = [];
        $stok = [];

        $no = 1;

        foreach ($rows as $row) {

            $id = $row->nama;

            if (!isset($stok[$id])) {
                $stok[$id] = 0;
            }

            $masuk = $row->tipe == 'masuk' ? $row->jumlah : 0;
            $keluar = $row->tipe == 'keluar' ? $row->jumlah : 0;

            $stok[$id] = $stok[$id] + $masuk - $keluar;

            $data[] = [
                'no' => $no++,
                'tanggal' => date('d-m-Y', strtotime($row->tanggal)),
                'nama' => $row->nama,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'stok' => $stok[$id],
                'stok_minimum' => $row->stok_minimum
            ];
        }

        $totalMasuk = collect($data)->sum('masuk');
        $totalKeluar = collect($data)->sum('keluar');
        $totalStok = end($stok) ?? 0;

        return view('laporan.index', compact(
            'data',
            'from',
            'to',
            'totalMasuk',
            'totalKeluar',
            'totalStok'
        ));
    }
}
