@extends('layouts.app')

@section('content')

<div class="table-section fade-in-up">

    <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">

        <h2 style="color:blue;">Laporan Stok</h2>

        <form method="GET" style="display:flex; gap:8px;">
            <input type="date" name="from" value="{{ $from }}" style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;">-
            <input type="date" name="to" value="{{ $to }}" style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;">
            <button type="submit" style="background:blue; color:white; padding:6px 14px; border:none; border-radius:6px; cursor:pointer;">
                Filter
            </button>
        </form>

    </div>
<!-- NAV LAPORAN -->
<div style="margin-bottom:15px; display:flex; gap:10px;">

    <!-- AKTIF -->
    
    <a href="{{ route('laporan.index') }}"
        style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px; text-decoration:none;">
        Laporan Stok
    </a>

    <!-- PINDAH KE KEUANGAN -->
    <a href="{{ route('laporan.keuangan') }}"
        style="background:#e5e7eb; color:#111; padding:8px 16px; border-radius:6px; text-decoration:none;">
        Laporan Keuangan
    </a>

</div>
    <div class="table-box">

        <table style="width:100%; text-align:center; border-collapse:collapse;">

            <!-- HEADER -->
            <thead style="background:#2563eb; color:white;">
                <tr>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">No</th>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">Tanggal</th>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">Nama Obat</th>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">Pemasukan</th>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">Pengeluaran</th>
                    <th style="border:1px solid #2563eb; padding:8px; text-align-last: center;">Stok Akhir</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody>
                @foreach($data as $item)
                <tr style="background: {{ $loop->even ? '#e0ecff' : '#ffffff' }};">
                    <td style="border:1px solid #2563eb; padding:8px; text-align-last: center;">{{ $loop->iteration }}</td>
                    <td style="border:1px solid #2563eb; padding:8px; text-align-last: center;">{{ $item['tanggal'] }}</td>
                    <td style="border:1px solid #2563eb; padding:8px; text-align-last: center;">{{ $item['nama'] }}</td>

                    <td style="border:1px solid #2563eb; padding:8px; color:green; text-align-last: center;">
                        {{ $item['jumlah_awal'] }}
                    </td>

                    <td style="border:1px solid #2563eb; padding:8px; color:red; text-align-last: center;">
                        {{ $item['keluar'] }}
                    </td>

                    <td style="border:1px solid #2563eb; padding:8px; font-weight:bold;
                        color: {{ $item['stok'] <= $item['stok_minimum'] ? 'red' : 'black' }};">
                        {{ $item['stok'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>

            <!-- TOTAL -->
            <tfoot>
                <tr style="background:#c7dfff; font-weight:bold;">
                    <td colspan="3" style="border:1px solid #2563eb; padding:10px;">
                        TOTAL
                    </td>

                    <td style="border:1px solid #2563eb; padding:10px; color:green;">
                        {{ $totalMasuk }}
                    </td>

                    <td style="border:1px solid #2563eb; padding:10px; color:red;">
                        {{ $totalKeluar }}
                    </td>

                    <td style="border:1px solid #2563eb; padding:10px;">
                        {{ $totalStok }}
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>

    <div style="margin-top:15px; display:flex; justify-content:flex-end; gap:10px;">
        <a href="{{ route('laporan.excel', request()->all()) }}"
            style="background:blue; color:white; padding:7px 14px; border-radius:6px; text-decoration:none;">
            Export Excel
        </a>

        <a href="{{ route('laporan.pdf', request()->all()) }}"
            style="background:red; color:white; padding:7px 14px; border-radius:6px; text-decoration:none;">
            Cetak PDF
        </a>
    </div>

</div>

@endsection