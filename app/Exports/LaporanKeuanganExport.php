<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanKeuanganExport implements FromArray, WithStyles, WithDrawings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data)->map(function ($item) {
            return (array) $item;
        });
    }

    public function array(): array
    {
        $rows = [];

        $user = session('user_name', 'Admin');

        // ==========================
        // HEADER
        // ==========================
        $rows[] = [''];
        $rows[] = ['LAPORAN PENJUALAN OBAT'];
        $rows[] = [''];

        $rows[] = [
            'Tanggal Cetak : ' . now()->format('d M Y H:i'),
            '',
            'Total Data : ' . $this->data->count()
        ];

        $rows[] = [
            'Dicetak Oleh  : ' . $user,
            '',
            'Total Penjualan : ' . number_format(
                $this->data->sum(fn($x) => $x['pemasukan'] ?? 0)
            )
        ];

        $rows[] = [
            'Status        : Laporan Penjualan',
            '',
            ''
        ];

        $rows[] = [''];

        // ==========================
        // TABLE HEADER
        // ==========================
        $rows[] = [
            'No',
            'Tanggal',
            'Nama Obat',
            'Jumlah',
            'Pemasukan',
            'Stok Real'
        ];

        // ==========================
        // DATA
        // ==========================
        $no = 1;

        foreach ($this->data as $item) {

            $rows[] = [
                $no++,
                $item['tanggal'] ?? '-',
                $item['nama'] ?? '-',
                $item['jumlah'] ?? 0,
                $item['pemasukan'] ?? 0,

                // 🔥 FIX ERROR
                // langsung ambil dari data controller
                $item['stok'] ?? 0,
            ];
        }

        // ==========================
        // TOTAL
        // ==========================
        $total = $this->data->sum(
            fn($x) => $x['pemasukan'] ?? 0
        );

        $rows[] = [
            '',
            '',
            '',
            'TOTAL PENJUALAN',
            $total,
            ''
        ];

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

        $sheet->getStyle('A2')
            ->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet->getStyle('A2')
            ->getAlignment()
            ->setHorizontal('center');

        // ==========================
        // INFO
        // ==========================
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A5:C5');
        $sheet->mergeCells('A6:C6');

        $sheet->mergeCells('D4:F4');
        $sheet->mergeCells('D5:F5');
        $sheet->mergeCells('D6:F6');

        $sheet->getStyle('A4:F6')
            ->getAlignment()
            ->setHorizontal('left');

        $sheet->getStyle('A4:F6')
            ->getAlignment()
            ->setWrapText(true);

        // ==========================
        // HEADER TABLE
        // ==========================
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")
            ->applyFromArray([
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
        // CENTER DATA
        // ==========================
        $sheet->getStyle("A9:F{$lastRow}")
            ->getAlignment()
            ->setHorizontal('center');

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
        $sheet->getStyle("D" . ($lastRow + 1) . ":F" . ($lastRow + 1))
            ->getFont()
            ->setBold(true);

        // ==========================
        // WIDTH
        // ==========================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(20);
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