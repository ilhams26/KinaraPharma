@extends('layouts.app')

@section('content')
    <div class="table-section fade-in-up">

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
                        <th>Detail Batch (Kadaluarsa, Sisa & Aksi)</th>
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
                                    <ul style="margin: 0; padding-left: 0; font-size: 13px; list-style: none;">
                                        @foreach($obat->batches->where('jumlah_sisa', '>', 0) as $batch)
                                            <li
                                                style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #eee; display: flex; justify-content: space-between; align-items: center;">
                                                <span>
                                                    <i class="fas fa-box-open" style="color: #888; margin-right: 5px;"></i>
                                                    {{ \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') }}
                                                    <strong style="color: var(--primary); margin-left: 5px;">(Sisa:
                                                        {{ $batch->jumlah_sisa }})</strong>
                                                </span>
                                                <button
                                                    onclick="showAdjustModal({{ $batch->id }}, '{{ $obat->nama }}', {{ $batch->jumlah_sisa }})"
                                                    style="background: var(--warning); color: #fff; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                                    <i class="fas fa-cog"></i> Sesuaikan
                                                </button>
                                            </li>
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

    <div class="modal-overlay" id="adjustStokModal">
        <div class="modal-box" style="max-width: 400px;">
            <h3 style="color: var(--warning);"><i class="fas fa-exclamation-triangle"></i> Penyesuaian Stok</h3>
            <form action="{{ route('staff.stok.adjust') }}" method="POST" style="text-align: left; margin-top: 20px;">
                @csrf
                <input type="hidden" name="batch_id" id="adjust_batch_id">

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Obat Terpilih</label>
                    <input type="text" id="adjust_nama_obat" readonly
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #f0f0f0; color: #666;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Tipe Penyesuaian</label>
                        <select name="tipe_penyesuaian" id="adjust_tipe" onchange="toggleLimitStok()" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                            <option value="keluar">Barang Keluar (-)</option>
                            <option value="masuk">Stok Bertambah (+)</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Jumlah</label>
                        <input type="number" name="jumlah" id="adjust_jumlah" required min="1" placeholder="Max: 0"
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Keterangan / Alasan</label>
                    <input type="text" name="keterangan" required placeholder="Contoh: Obat pecah/rusak/expired"
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="hideAdjustModal()">Batal</button>
                    <button type="submit" class="btn-confirm"
                        style="background: var(--warning); color: #fff;">Sesuaikan</button>
                </div>
            </form>
        </div>
    </div>

    <script>

        let stokMaksimalSaatIni = 0;

        function showAdjustModal(batchId, namaObat, stokMaksimal) {
            let modal = document.getElementById('adjustStokModal');
            if (modal) {
                stokMaksimalSaatIni = stokMaksimal;

                document.getElementById('adjust_batch_id').value = batchId;
                document.getElementById('adjust_nama_obat').value = namaObat + ' (Sisa ' + stokMaksimal + ')';

                document.getElementById('adjust_tipe').value = 'keluar';

                toggleLimitStok();

                modal.classList.add('active');
            }
        }

        function toggleLimitStok() {
            let tipe = document.getElementById('adjust_tipe').value;
            let inputJumlah = document.getElementById('adjust_jumlah');

            if (tipe === 'keluar') {
                inputJumlah.max = stokMaksimalSaatIni;
                inputJumlah.placeholder = 'Max: ' + stokMaksimalSaatIni;
            } else if (tipe === 'masuk') {
                inputJumlah.removeAttribute('max');
                inputJumlah.placeholder = 'Jumlah tambahan';
            }
        }

        function hideAdjustModal() {
            let modal = document.getElementById('adjustStokModal');
            if (modal) {
                modal.classList.remove('active');
                modal.querySelector('form').reset();
            }
        }
    </script>
@endsection