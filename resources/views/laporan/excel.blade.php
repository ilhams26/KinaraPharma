<table>
    <!-- JUDUL -->
    <tr>
        <td colspan="8" style="text-align:center; font-weight:bold;">
            LAPORAN INVENTORI OBAT - KINARA PHARMA
        </td>
    </tr>

    <!-- INFO -->
    <tr>
        <td colspan="4">Tanggal Cetak: {{ now()->format('d M Y H:i') }}</td>
        <td colspan="4">Dicetak Oleh: {{ auth()->user()->name ?? 'Admin' }}</td>
    </tr>

    <tr>
        <td colspan="4">Total Obat: {{ count($data) }} item</td>
        <td colspan="4">
            Total Transaksi:
            {{ collect($data)->sum('pemasukan') + collect($data)->sum('pengeluaran') }} unit
        </td>
    </tr>

    <tr></tr>

    <!-- HEADER -->
    <tr>
        <th>No</th>
        <th>Nama Obat</th>
        <th>Stok Awal</th>
        <th>Pemasukan</th>
        <th>Pengeluaran</th>
        <th>Stok Akhir</th>
        <th>Status</th>
        <th>Kadaluarsa</th>
    </tr>

    <!-- DATA -->
    @foreach($data as $item)
    <tr>
        <td>{{ $item->no }}</td>
        <td style="text-align:left;">{{ $item->nama }}</td>
        <td>{{ $item->stok_awal }}</td>
        <td>{{ $item->pemasukan }}</td>
        <td>{{ $item->pengeluaran }}</td>
        <td>{{ $item->stok_akhir }}</td>
        <td>{{ $item->status }}</td>
        <td>{{ $item->expired }}</td>
    </tr>
    @endforeach

    <!-- TOTAL -->
    <tr>
        <td colspan="3">TOTAL</td>
        <td>{{ collect($data)->sum('pemasukan') }}</td>
        <td>{{ collect($data)->sum('pengeluaran') }}</td>
        <td>{{ collect($data)->sum('stok_akhir') }}</td>
        <td colspan="2"></td>
    </tr>

    <tr></tr>

    <tr>
        <td colspan="8" style="text-align:center;">
            Sistem Manajemen Apotek - Kinara Pharma
        </td>
    </tr>

</table>