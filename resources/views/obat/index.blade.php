@extends('layouts.app')

@section('content')
<div class="table-section">
    <div class="table-header">
        <h2 style="color: var(--primary-hover);">Data Obat</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" placeholder="Telusuri..." style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; flex: 1;">
            <a href="#" class="btn-primary"><i class="fas fa-plus"></i> Tambah Obat</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-laporan">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Stok Real</th>
                    <th>Status</th>
                    <th>Kadaluarsa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>OB001</td>
                    <td style="min-width: 150px;">Paracetamol 500mg</td> <td>Demam</td>
                    <td><strong>145</strong></td>
                    <td><span class="badge bg-success">AMAN</span></td>
                    <td style="min-width: 100px;">2026-04-02</td>
                    <td style="min-width: 80px;">
                        <button style="border:none; background:none; color:var(--primary); cursor:pointer; margin-right:10px;"><i class="fas fa-edit"></i></button>
                        <button style="border:none; background:none; color:var(--danger); cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                </tbody>
        </table>
    </div>
</div>
@endsection