@extends('layouts.app')

@section('content')

<div class="table-section fade-in-up">

    <!-- HEADER -->
    <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

        <h2 style="color: var(--primary-hover); margin:0;">Laporan</h2>

        <form method="GET" class="filter-box" style="display:flex; align-items:center; gap:8px;">

            <input type="date" name="from" value="{{ $from }}"
                style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;">

            <span>-</span>

            <input type="date" name="to" value="{{ $to }}"
                style="padding:6px 10px; border:2px solid blue; border-radius:6px; outline:none;">

            <button type="submit"
                style="background:blue; color:white; padding:6px 14px; border:none; border-radius:6px; cursor:pointer;">
                Filter
            </button>

        </form>
    </div>

    <!-- TABLE -->
    <div class="table-box" style="background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.08);">

        <div class="table-responsive">

            <table style="width:100%; border-collapse:collapse; text-align:center;">

                <thead class="bg-blue-600 text-white">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th style="text-align:left;">Nama Obat</th>
    <th>Pemasukan</th>
    <th>Pengeluaran</th>
    <th>Stok Akhir</th>
</tr>
</thead>

<tbody>
@foreach($data as $item)
<tr>
    <td>{{ $item['no'] }}</td>
    <td>{{ $item['tanggal'] }}</td>

    <td style="text-align:left;">
        {{ $item['nama'] }}
    </td>

    <td style="color:green;">
        {{ $item['masuk'] }}
    </td>

    <td style="color:red;">
        {{ $item['keluar'] }}
    </td>

    <td style="
        font-weight:bold;
        color: {{ $item['stok'] <= $item['stok_minimum'] ? 'red' : 'black' }};
    ">
        {{ $item['stok'] }}
    </td>
</tr>
@endforeach
</tbody>

                <!-- TOTAL -->
                <tfoot style="background:#f5f5f5; font-weight:bold;">
                    <tr>
                        <td colspan="3">TOTAL</td>
                        <td style="color:green;">{{ $totalMasuk ?? 0 }}</td>
                        <td style="color:red;">{{ $totalKeluar ?? 0 }}</td>
                        <td>{{ $totalStok ?? 0 }}</td>
                        <td></td>
                    </tr>
                </tfoot>

            </table>

        </div>
    </div>

    <!-- BUTTON -->
    <div class="btn-group" style="margin-top:15px; display:flex; gap:10px;">

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