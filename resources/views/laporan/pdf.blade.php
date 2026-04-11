<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventori</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 25%;
            font-size: 80px;
            color: rgba(0,0,0,0.05);
            transform: rotate(-30deg);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2563eb;
        }

        .header p {
            margin: 2px;
            font-size: 11px;
            color: #555;
        }

        .info-box {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-box td {
            padding: 4px;
        }

        .info-title {
            font-weight: bold;
        }

        .summary {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .summary table {
            width: 100%;
        }

        .summary td {
            padding: 6px;
            background: #f1f5f9;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
        }

        table.main th {
            background: #2563eb;
            color: white;
            padding: 8px;
        }

        table.main td {
            padding: 6px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }

        table.main tr:nth-child(even) {
            background: #f9fafb;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .ttd {
            width: 200px;
            text-align: center;
            float: right;
        }

        .ttd-line {
            margin-top: 60px;
            border-top: 1px solid black;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <p>Laporan Inventori Obat</p>
        <p>Sistem Manajemen Apotek</p>
    </div>

    <!-- INFO -->
    <table class="info-box">
        <tr>
            <td class="info-title">Tanggal Cetak</td>
            <td>: {{ now()->format('d M Y H:i') }}</td>

            <td class="info-title">Total Obat</td>
            <td>: {{ count($data) }} item</td>
        </tr>

        <tr>
            <td class="info-title">Dicetak Oleh</td>
            <td>: {{ auth()->user()->name ?? 'Admin' }}</td>

            <td class="info-title">Total Pemasukan</td>
            <td>: {{ collect($data)->sum('pemasukan') }} unit</td>
        </tr>

        <tr>
            <td class="info-title">Status</td>
            <td>: Laporan Lengkap</td>

            <td class="info-title">Total Pengeluaran</td>
            <td>: {{ collect($data)->sum('pengeluaran') }} unit</td>
        </tr>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <table>
            <tr>
                <td><b>Total Stok Awal:</b> {{ collect($data)->sum('stok_awal') }}</td>
                <td><b>Total Stok Akhir:</b> {{ collect($data)->sum('stok_akhir') }}</td>
            </tr>
        </table>
    </div>

    <!-- TABLE -->
    <table class="main">
        <thead>
            <tr>
                <th>Obat</th>
                <th>Stok Awal</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->stok_awal }}</td>
                <td>{{ $item->pemasukan }}</td>
                <td>{{ $item->pengeluaran }}</td>
                <td>{{ $item->stok_akhir }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <div class="ttd">
            <p>Cirebon, {{ now()->format('d M Y') }}</p>
            <p><b>Mengetahui,</b></p>

            <div class="ttd-line"></div>
            <p><b>Pemilik Apotik Kinara</b></p>
        </div>
    </div>

</body>
</html>