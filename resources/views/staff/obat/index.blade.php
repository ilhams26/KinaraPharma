@extends('layouts.app')

@section('content')
    <div class="table-section fade-in-up">

        @if(session('success'))
            <div style="background: var(--success); color: white; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h2 style="color: var(--primary-hover);">Kelola Obat (Staff)</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" placeholder="Telusuri nama obat..."
                    style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; min-width: 250px;">
                <button onclick="showAddModal()" class="btn-primary"><i class="fas fa-plus"></i> Tambah Obat</button>
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
                            <td style="font-weight: bold; min-width: 150px;">{{ $obat->nama }}</td>
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
                            <td style="min-width: 80px; display: flex; gap: 10px; align-items: center;">
                                <button style="border:none; background:none; color:var(--primary); cursor:pointer;"
                                    title="Edit"><i class="fas fa-edit"></i></button>

                                <form action="{{ route('staff.obat.destroy', $obat->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus obat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="border:none; background:none; color:var(--danger); cursor:pointer;"
                                        title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="addObatModal">
        <div class="modal-box" style="max-width: 500px;">
            <h3>Tambah Obat Baru</h3>
            <form action="{{ route('staff.obat.store') }}" method="POST" style="text-align: left; margin-top: 20px;">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Nama Obat</label>
                    <input type="text" name="nama" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Kategori</label>
                        <select name="kategori_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                            <option value="1">Demam</option>
                            <option value="2">Batuk & Flu</option>
                            <option value="3">Sakit Kepala</option>
                            <option value="4">Vitamin</option>
                            <option value="5">Maag</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Jenis Obat</label>
                        <select name="jenis" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                            <option value="biasa">Biasa (Bebas)</option>
                            <option value="keras">Keras (Resep)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Harga (Rp)</label>
                        <input type="number" name="harga" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Stok Awal</label>
                        <input type="number" name="stok_awal" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Tanggal Kadaluarsa</label>
                    <input type="date" name="expired_date" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="hideAddModal()">Batal</button>
                    <button type="submit" class="btn-confirm">Simpan Obat</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="editObatModal">
    <div class="modal-box" style="max-width: 400px;">
        <h3>Edit Data Obat</h3>
        <form action="#" method="POST" style="text-align: left; margin-top: 15px;">
            @csrf
            @method('PUT') <input type="hidden" name="id" id="edit_id">

            <div style="margin-bottom: 12px;">
                <label style="font-weight: bold; display: block; margin-bottom: 3px;">Nama Obat</label>
                <input type="text" name="nama" id="edit_nama" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-weight: bold; display: block; margin-bottom: 3px;">Kategori</label>
                <select name="kategori_id" id="edit_kategori_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="1">Demam</option>
                    <option value="2">Batuk</option>
                    <option value="3">Vitamin</option>
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-weight: bold; display: block; margin-bottom: 3px;">Jenis</label>
                <select name="jenis" id="edit_jenis" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="biasa">Biasa (Bebas)</option>
                    <option value="keras">Keras (Resep)</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 3px;">Harga (Rp)</label>
                <input type="number" name="harga" id="edit_harga" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="hideEditModal()">Batal</button>
                <button type="submit" class="btn-confirm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection