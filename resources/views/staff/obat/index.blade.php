@extends('layouts.app')

@section('content')
    <div class="table-section fade-in-up">
        <div class="table-header">
            <h2 style="color: var(--primary-hover);">Kelola Obat (Staff)</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" placeholder="Telusuri nama obat..."
                    style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; min-width: 250px;">
                <a href="#" class="btn-primary"><i class="fas fa-plus"></i> Tambah Obat</a>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($obats as $obat)
                        @php $totalStok = $obat->batches->sum('jumlah_sisa'); @endphp
                        <tr>
                            <td>OBT-{{ str_pad($obat->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="font-weight: normal; min-width: 150px;">{{ $obat->nama }}</td>
                            <td>{{ $obat->kategori->nama ?? '-' }}</td>
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
                            <td style="min-width: 80px;">
                                <button
                                    style="border:none; background:none; color:var(--primary); cursor:pointer; margin-right:10px;"
                                    title="Edit"><i class="fas fa-edit"></i></button>
                                <button style="border:none; background:none; color:var(--danger); cursor:pointer;"
                                    title="Hapus"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection