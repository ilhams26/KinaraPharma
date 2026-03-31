@extends('layouts.app')

@section('content')
    <div class="table-section">
        <div class="table-header">
            <h2 style="color: var(--primary-hover);">Data Obat</h2>
            <div style="display: flex; gap: 10px;">
                <input type="text" placeholder="Telusuri..."
                    style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px;">
                <a href="#" class="btn-primary"><i class="fas fa-plus"></i> Tambah Obat</a>
            </div>
        </div>

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
                    <td>Paracetamol 500mg</td>
                    <td>Demam</td>
                    <td><strong>145</strong></td>
                    <td><span class="badge bg-success">AMAN</span></td>
                    <td>2026-04-02</td>
                    <td>
                        <button style="border:none; background:none; color:var(--primary); cursor:pointer;"><i
                                class="fas fa-edit"></i></button>
                        <button style="border:none; background:none; color:var(--danger); cursor:pointer;"><i
                                class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>OB002</td>
                    <td>Amoxicillin 500mg</td>
                    <td>Antibiotik</td>
                    <td><strong>2</strong></td>
                    <td><span class="badge bg-danger">HABIS</span></td>
                    <td>2026-10-15</td>
                    <td>
                        <button style="border:none; background:none; color:var(--primary); cursor:pointer;"><i
                                class="fas fa-edit"></i></button>
                        <button style="border:none; background:none; color:var(--danger); cursor:pointer;"><i
                                class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>OB003</td>
                    <td>Vitamin C</td>
                    <td>Vitamin</td>
                    <td><strong>15</strong></td>
                    <td><span class="badge bg-warning">MENIPIS</span></td>
                    <td>2025-12-01</td>
                    <td>
                        <button style="border:none; background:none; color:var(--primary); cursor:pointer;"><i
                                class="fas fa-edit"></i></button>
                        <button style="border:none; background:none; color:var(--danger); cursor:pointer;"><i
                                class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection