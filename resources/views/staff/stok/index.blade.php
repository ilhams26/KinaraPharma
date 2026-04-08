@extends('layouts.app')

@section('content')
    <div class="table-section fade-in-up">

        @if(session('success'))
            <div class="alert-auto-close"
                style="background: var(--success); color: white; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h2 style="color: var(--primary-hover);">Kelola Stok & Batch</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Telusuri obat..."
                    style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; min-width: 250px;">
                <button onclick="showAddStokModal()" class="btn-primary"><i class="fas fa-plus"></i> Tambah Stok</button>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Total Stok Real</th>
                        <th>Detail Batch (Kadaluarsa & Sisa)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($obats as $obat)
                        @php $totalStok = $obat->batches->sum('jumlah_sisa'); @endphp
                        <tr>
                            <td style="font-weight: bold;">{{ $obat->nama }}</td>
                            <td><strong>{{ $totalStok }}</strong></td>
                            <td>
                                @if($obat->batches->where('jumlah_sisa', '>', 0)->count() > 0)
                                    <ul style="margin: 0; padding-left: 15px; font-size: 13px;">
                                        @foreach($obat->batches->where('jumlah_sisa', '>', 0)->sortBy('expired_date') as $batch)
                                            <li>{{ \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') }} <strong
                                                    style="color: var(--primary);">(Sisa: {{ $batch->jumlah_sisa }})</strong></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color: var(--danger); font-weight: bold;">Stok Kosong</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="addStokModal">
        <div class="modal-box" style="max-width: 400px;">
            <h3>Tambah Stok Baru</h3>
            <form action="{{ route('staff.stok.store') }}" method="POST" style="text-align: left; margin-top: 20px;">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Pilih Obat</label>
                    <select name="obat_id" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        @foreach($obats as $o)
                            <option value="{{ $o->id }}">{{ $o->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Jumlah Tambahan</label>
                    <input type="number" name="jumlah" required min="1"
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Tanggal Kadaluarsa</label>
                    <input type="date" name="expired_date" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="hideAddStokModal()">Batal</button>
                    <button type="submit" class="btn-confirm">Simpan Stok</button>
                </div>
            </form>
        </div>
    </div>
@endsection