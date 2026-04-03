@extends('layouts.app')

@section('content')
<div class="table-section fade-in-up">
    <div class="table-header">
        <h2 style="color: var(--primary-hover);">Data Obat</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" placeholder="Telusuri nama obat..." style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; min-width: 250px;">
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Stok Real</th>
                    <th>Status</th>
                    <th>Kadaluarsa</th>
                    </tr>
            </thead>`
            <tbody>
                @foreach ($obats as $obat)
                <tr>
                    <td>OBT-{{ str_pad($obat->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight: bold; min-width: 150px;">{{ $obat->nama }}</td>
                    <td>{{ $obat->kategori->nama ?? '-' }}</td>
                    
                    @php
                        $totalStok = $obat->batches->sum('jumlah_sisa');
                    @endphp
                    
                    <td><strong>{{ $totalStok }}</strong></td>
                    
                    <td>
                        @if($totalStok == 0)
                            <span class="badge bg-danger">HABIS</span>
                        @elseif($totalStok <= $obat->stok_minimum)
                            <span class="badge bg-warning">MENIPIS</span>
                        @else
                            <span class="badge bg-success">AMAN</span>
                        @endif
                    </td>
                    
                    <td style="min-width: 100px;">
                        @php
                            $batchTerdekat = $obat->batches->where('jumlah_sisa', '>', 0)->sortBy('expired_date')->first();
                        @endphp
                        {{ $batchTerdekat ? \Carbon\Carbon::parse($batchTerdekat->expired_date)->format('d M Y') : 'Tidak Ada' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection