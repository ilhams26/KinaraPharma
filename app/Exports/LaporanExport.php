<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanExport implements FromArray, WithStyles, WithDrawings
{
    protected $data;

    public function __construct($data)
    {
        // 🔥 paksa jadi array (anti error object)
        $this->data = collect($data)->map(function ($item) {
            return (array) $item;
        });
    }

    public function array(): array
    {
        $rows = [];

        // 🔥 USER AMAN (ANTI ERROR)
        $user = session('user_name', 'staff');

        // ==========================
        // HEADER
        // ==========================
        $rows[] = [''];
        $rows[] = ['LAPORAN INVENTORI OBAT'];
        $rows[] = [''];

        // ✅ FIX INFORMASI (TIDAK KEPOTONG & RAPI)
        $rows[] = ['Tanggal Cetak : ' . now()->format('d M Y H:i'), '', 'Total Obat : ' . $this->data->count() . ' item'];
        $rows[] = ['Dicetak Oleh  : ' . $user, '', 'Total Pemasukan : ' . $this->data->sum(fn($x) => $x['jumlah_awal'] ?? 0) . ' unit'];
        $rows[] = ['Status        : Laporan Lengkap', '', 'Total Pengeluaran : ' . $this->data->sum(fn($x) => $x['keluar'] ?? 0) . ' unit'];

        $rows[] = [''];

        // ==========================
        // TABLE HEADER
        // ==========================
        $rows[] = ['No','Tanggal','Nama Obat','Pemasukan','Pengeluaran','Stok Akhir'];

        // ==========================
        // DATA
        // ==========================
        $no = 1;

        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                $item['tanggal'] ?? '-',
                $item['nama'] ?? '-',
                $item['jumlah_awal'] ?? 0,
                $item['keluar'] ?? 0,
                $item['stok'] ?? 0,
            ];
        }

        // ==========================
        // TOTAL (STOK REAL FIX)
        // ==========================
        $totalMasuk = $this->data->sum(fn($x) => $x['jumlah_awal'] ?? 0);
        $totalKeluar = $this->data->sum(fn($x) => $x['keluar'] ?? 0);

        $stokAkhir = $this->data
            ->groupBy('nama')
            ->map(function ($items) {
                $last = collect($items)->last();
                return $last['stok'] ?? 0;
            })
            ->sum();

        $rows[] = ['', '', 'TOTAL', $totalMasuk, $totalKeluar, $stokAkhir];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = 8;
        $lastRow = $headerRow + $this->data->count();

        // ==========================
        // JUDUL
        // ==========================
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // ==========================
        // ✅ FIX INFO BIAR RAPI
        // ==========================
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A5:C5');
        $sheet->mergeCells('A6:C6');

        $sheet->mergeCells('D4:F4');
        $sheet->mergeCells('D5:F5');
        $sheet->mergeCells('D6:F6');

        $sheet->getStyle('A4:F6')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A4:F6')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:F6')->getAlignment()->setWrapText(true);

        // ==========================
        // HEADER TABLE
        // ==========================
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '2563EB']
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ]
        ]);

        // ==========================
        // CENTER SEMUA
        // ==========================
        $sheet->getStyle("A9:F{$lastRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        // ==========================
        // BORDER
        // ==========================
        $sheet->getStyle("A{$headerRow}:F" . ($lastRow + 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        // ==========================
        // TOTAL STYLE
        // ==========================
        $sheet->getStyle("C" . ($lastRow + 1) . ":F" . ($lastRow + 1))
            ->getFont()->setBold(true);

        // ==========================
        // WIDTH
        // ==========================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        return [];
    }

    public function drawings()
    {
        $path = public_path('images/logo.png');

        if (!file_exists($path)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($path);
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}