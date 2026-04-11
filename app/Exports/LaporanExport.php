<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromView, ShouldAutoSize, WithStyles, WithEvents
{
    protected $dari;
    protected $sampai;

    public function __construct($dari = null, $sampai = null)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function view(): View
    {
        $query = DB::table('obat')
            ->leftJoin('pergerakan_stok', 'obat.id', '=', 'pergerakan_stok.obat_id')
            ->select(
                'obat.nama',
                DB::raw("COALESCE(SUM(CASE WHEN tipe='masuk' THEN jumlah END),0) as pemasukan"),
                DB::raw("COALESCE(SUM(CASE WHEN tipe='keluar' THEN jumlah END),0) as pengeluaran")
            )
            ->groupBy('obat.id', 'obat.nama');

        if ($this->dari && $this->sampai) {
            $query->whereBetween('pergerakan_stok.created_at', [$this->dari, $this->sampai]);
        }

        $data = $query->get();

        $no = 1;
        foreach ($data as $item) {
            $item->no = $no++;
            $item->stok_awal = 0;
            $item->stok_akhir = $item->stok_awal + $item->pemasukan - $item->pengeluaran;
            $item->status = $item->stok_akhir <= 20 ? 'WARNING' : 'AMAN';
            $item->expired = '-';
        }

        return view('laporan.excel', compact('data'));
    }

    // STYLE
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // judul
            5 => ['font' => ['bold' => true]], // header tabel
        ];
    }

    // EVENT (BORDER, WARNA DLL)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $lastRow = $sheet->getHighestRow();

                // BORDER SEMUA TABEL
                $sheet->getStyle("A5:H{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // HEADER WARNA
                $sheet->getStyle('A5:H5')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'D9D9D9']
                    ],
                    'font' => ['bold' => true]
                ]);

                // ALIGN CENTER
                $sheet->getStyle("A5:H{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('center');

                // BOLD TOTAL
                $sheet->getStyle("A{$lastRow}:H{$lastRow}")
                    ->getFont()
                    ->setBold(true);
            }
        ];
    }
}