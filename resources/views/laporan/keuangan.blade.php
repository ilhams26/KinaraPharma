@extends('layouts.app')

@section('content')

<div class="table-section fade-in-up">

    <!-- HEADER -->
    <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">

        <h2 style="color:blue;">Laporan Keuangan</h2>

        <form method="GET" style="display:flex; gap:8px;">
            <input type="date" name="from" value="{{ $from }}"
                style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;"> -

            <input type="date" name="to" value="{{ $to }}"
                style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;">

            <button type="submit"
                style="background:blue; color:white; padding:6px 14px; border:none; border-radius:6px; cursor:pointer;">
                Filter
            </button>
        </form>

    </div>

    <!-- NAV LAPORAN -->
    <div style="margin:15px 0; display:flex; gap:10px;">

        <!-- KE STOK -->
        <a href="{{ route('laporan.index') }}"
            style="background:#e5e7eb; color:#111; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:500; transition:0.2s;"
            onmouseover="this.style.background='#d1d5db'"
            onmouseout="this.style.background='#e5e7eb'">
            Laporan Stok
        </a>

        <!-- AKTIF -->
        <a href="{{ route('laporan.keuangan') }}"
            style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold;">
            Laporan Keuangan
        </a>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table style="width:100%; border-collapse:collapse; text-align:center;">

            <!-- HEADER -->
            <thead style="background:#2563eb; color:white;">
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">Tanggal</th>
                    <th style="text-align: center;">Nama Obat</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: center;">Pemasukan</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody>
                @foreach($data as $item)
                <tr style="background: {{ $loop->even ? '#e0ecff' : '#ffffff' }}">
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ date('d-m-Y H:i', strtotime($item->tanggal)) }}</td>

                    <td>{{ $item->nama }}</td>

                    <td>{{ $item->jumlah }}</td>

                    <td style="color:green;">
                        {{ number_format($item->pemasukan) }}
                    </td>
                </tr>
                @endforeach
            </tbody>

            <!-- TOTAL -->
            <tfoot>
                <tr style="background:#c7dfff; font-weight:bold;">
                    <td colspan="4" style="border:1px solid #2563eb; padding:10px;">
                        TOTAL PENJUALAN
                    </td>

                    <td style="border:1px solid #2563eb; padding:10px; color:green;">
                        {{ number_format($totalMasuk) }}
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>

    <!-- EXPORT BUTTON -->
    <div style="margin-top:15px; display:flex; justify-content:flex-end; gap:10px;">

        <a href="{{ route('laporan.keuangan.excel', request()->all()) }}"
            style="background:blue; color:white; padding:7px 14px; border-radius:6px; text-decoration:none;">
            Export Excel
        </a>

        <a href="{{ route('laporan.keuangan.pdf', request()->all()) }}"
            style="background:red; color:white; padding:7px 14px; border-radius:6px; text-decoration:none;">
            Cetak PDF
        </a>

    </div>

</div>

@endsection