@extends('layouts.app')

@section('content')
    <div>
        @if(session('success'))
            <div class="alert-auto-close"
                style="background: var(--success); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="dashboard-content" style="flex-direction: column;">

            <div class="table-section" style="width: 100%; margin-bottom: 30px;">
                <div class="table-header">
                    <h2 style="color: var(--primary-hover);"><i class="fas fa-shopping-bag"></i> Daftar Pesanan Masuk</h2>
                </div>

                <div
                    style="padding: 20px; text-align: center; color: var(--text-muted); background: #f9f9f9; border-radius: 8px;">
                    <i class="fas fa-clipboard-list" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>Daftar pesanan dari pelanggan akan muncul di sini.</p>
                </div>
            </div>

            <div class="table-section" style="width: 100%; border-top: 4px solid var(--danger); padding-top: 20px;">
                <div class="table-header">
                    <h2 style="color: var(--danger);"><i class="fas fa-file-prescription"></i> Validasi Resep Obat Keras
                    </h2>
                </div>

                @if($prescriptions->isEmpty())
                    <div
                        style="text-align: center; color: var(--text-muted); padding: 30px; background: #f9f9f9; border-radius: 8px;">
                        <i class="fas fa-check-circle" style="font-size: 40px; color: var(--success); margin-bottom: 15px;"></i>
                        <p>Tidak ada antrean resep dokter. Semua sudah tervalidasi.</p>
                    </div>
                @else
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 15px;">
                        @foreach($prescriptions as $item)
                            <div class="card"
                                style="padding: 15px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <div
                                    style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                                    <span style="font-size: 12px; color: var(--text-muted);">
                                        <i class="far fa-clock"></i> {{ $item->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <span
                                        style="background: var(--warning); color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                        Menunggu
                                    </span>
                                </div>

                                <div style="display: flex; gap: 15px;">
                                    <div style="flex: 1;">
                                        <a href="{{ asset('storage/' . $item->foto_resep) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $item->foto_resep) }}" alt="Foto Resep"
                                                style="width: 100%; height: 100px; object-fit: cover; border-radius: 6px; cursor: zoom-in;">
                                        </a>
                                        <div
                                            style="text-align: center; font-size: 10px; color: var(--text-muted); margin-top: 4px;">
                                            Klik untuk perbesar</div>
                                    </div>

                                    <div style="flex: 2;">
                                        <p style="margin: 0 0 5px 0; font-size: 13px; color: var(--text-muted);">Pembeli:</p>
                                        <p style="margin: 0 0 10px 0; font-weight: bold; color: var(--text);">
                                            {{ $item->user->username ?? 'Guest' }}</p>

                                        <p style="margin: 0 0 5px 0; font-size: 13px; color: var(--text-muted);">Untuk Obat:</p>
                                        <p style="margin: 0 0 15px 0; font-weight: bold; color: var(--danger);">
                                            {{ $item->obat->nama ?? 'Obat Dihapus' }}</p>

                                        <div style="display: flex; gap: 10px;">
                                            <form action="{{ route('staff.prescriptions.validate', $item->id) }}" method="POST"
                                                style="flex: 1;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-primary"
                                                    style="width: 100%; justify-content: center; background: var(--success); padding: 8px; font-size: 13px;"
                                                    onclick="return confirm('Yakin setujui resep ini?')">
                                                    <i class="fas fa-check"></i> ACC
                                                </button>
                                            </form>

                                            <form action="{{ route('staff.prescriptions.reject', $item->id) }}" method="POST"
                                                style="flex: 1;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-primary"
                                                    style="width: 100%; justify-content: center; background: var(--danger); padding: 8px; font-size: 13px;"
                                                    onclick="return confirm('Yakin tolak dan hapus resep ini?')">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection