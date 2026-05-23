<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            position: relative;
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
        <img src="{{ public_path('images/logo.png') }}" style="height:60px;">
        <p>Laporan Keuangan (Penjualan Obat)</p>
        <p>Sistem Manajemen Apotek</p>
    </div>

    <!-- INFO -->
    <table class="info-box">
        <tr>
            <td class="info-title">Tanggal Cetak</td>
            <td>: {{ now()->format('d M Y H:i') }}</td>

            <td class="info-title">Total Transaksi</td>
            <td>: {{ count($data) }} item</td>
        </tr>

        <tr>
            <td class="info-title">Dicetak Oleh</td>
            <td>: {{ auth()->user()->name ?? 'Admin' }}</td>

            <td class="info-title">Total Penjualan</td>
            <td>: Rp {{ number_format($totalMasuk) }}</td>
        </tr>

        <tr>
            <td class="info-title">Status</td>
            <td>: Laporan Penjualan</td>

            <td></td>
            <td></td>
        </tr>
    </table>
    
    <!-- TABLE -->
    <table class="main">

        <thead>
            <tr>
                <th style="width:5%; text-align:center;">No</th>
                <th style="width:23%; text-align:center;">Tanggal</th>
                <th style="width:30%; text-align:center;">Obat</th>
                <th style="text-align:center;">Jumlah</th>
                <th style="text-align:center;">Pemasukan</th>
                <th style="text-align:center;">Stok Real</th>
            </tr>
        </thead>

        <tbody>

            @foreach($data as $item)
            <tr>

                <td style="text-align:center;">
                    {{ $loop->iteration }}
                </td>

                <td style="text-align:center;">
                    {{ date('d-m-Y H:i', strtotime($item->tanggal)) }}
                </td>

                <td style="text-align:left;">
                    {{ $item->nama }}
                </td>

                <td style="text-align:center;">
                    {{ $item->jumlah }}
                </td>

                <td style="text-align:right;">
                    Rp {{ number_format($item->pemasukan) }}
                </td>

                <td style="text-align:center;">
                    {{ $item->stok }}
                </td>

            </tr>
            @endforeach

            <!-- TOTAL -->
            <tr style="background:#e2e8f0;">

                <td colspan="4"
                    style="text-align:right;
                    font-weight:bold;
                    border-top:2px solid #2563eb;
                    color:#0d2b69;">

                    TOTAL PENJUALAN

                </td>

                <td style="font-weight:bold;
                    border-top:2px solid #2563eb;
                    color:#0d2b69;">

                    Rp {{ number_format($totalMasuk) }}

                </td>

                <td style="border-top:2px solid #2563eb;">
                </td>

            </tr>

        </tbody>

    </table>

    <!-- FOOTER -->
    <div class="footer">

        <div class="ttd">

            <p>Indramayu, {{ now()->format('d M Y') }}</p>

            <p><b>Mengetahui,</b></p>

            <div class="ttd-line"></div>

            <p><b>Pemilik Apotik Kinara</b></p>

        </div>

    </div>

</body>
</html>