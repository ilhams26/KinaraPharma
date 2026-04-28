@extends('layouts.app')

@section('content')
    <div>
        <div class="dashboard-content" style="display: flex; gap: 20px; align-items: stretch; flex-wrap: wrap;">

            <div class="table-section"
                style="flex: 2; min-width: 300px; display: flex; flex-direction: column; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="table-header" style="margin-bottom: 20px;">
                    <h2 style="color: var(--primary-hover); margin: 0;"><i class="fas fa-shopping-bag"></i> Daftar Pesanan
                        Masuk</h2>
                </div>

                <div
                    style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--text-muted); background: #f9f9f9; border-radius: 8px; padding: 30px;">
                    <i class="fas fa-clipboard-list" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">Daftar pesanan dari pelanggan akan muncul di sini.</p>
                </div>
            </div>

            <div class="table-section"
                style="flex: 1; min-width: 300px; display: flex; flex-direction: column; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 4px solid var(--danger);">
                <div class="table-header" style="margin-bottom: 20px;">
                    <h2 style="color: var(--danger); margin: 0;"><i class="fas fa-file-prescription"></i> Validasi Resep
                        Obat Keras</h2>
                </div>

                <div style="flex: 1; display: flex; flex-direction: column;" id="resep-container">
                    @if($prescriptions->isEmpty())
                        <div id="empty-resep-state"
                            style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--text-muted); padding: 30px; background: #f9f9f9; border-radius: 8px;">
                            <i class="fas fa-check-circle"
                                style="font-size: 40px; color: var(--success); margin-bottom: 15px;"></i>
                            <p style="margin: 0;">Tidak ada antrean resep. Semua sudah tervalidasi.</p>
                        </div>
                    @else
                        <div id="empty-resep-state"
                            style="display: none; flex: 1; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: var(--text-muted); padding: 30px; background: #f9f9f9; border-radius: 8px;">
                            <i class="fas fa-check-circle"
                                style="font-size: 40px; color: var(--success); margin-bottom: 15px;"></i>
                            <p style="margin: 0;">Tidak ada antrean resep. Semua sudah tervalidasi.</p>
                        </div>

                        <div id="resep-list" style="display: flex; flex-direction: column; gap: 15px;">
                            @foreach($prescriptions as $item)
                                <div class="card resep-card" id="resep-card-{{ $item->id }}"
                                    style="padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; transition: all 0.3s ease;">
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
                                                {{ $item->user->username ?? 'Guest' }}
                                            </p>

                                            <p style="margin: 0 0 5px 0; font-size: 13px; color: var(--text-muted);">Untuk Obat:</p>
                                            <p style="margin: 0 0 15px 0; font-weight: bold; color: var(--danger);">
                                                {{ $item->obat->nama ?? 'Obat Dihapus' }}
                                            </p>

                                            <div style="display: flex; gap: 10px;">
                                                <button type="button" class="btn-primary"
                                                    style="flex: 1; background: var(--success); padding: 8px; font-size: 13px; border: none; border-radius: 5px; color: white; cursor: pointer;"
                                                    onclick="prosesResep({{ $item->id }}, '{{ route('staff.prescriptions.validate', $item->id) }}', 'PUT', 'ACC')">
                                                    <i class="fas fa-check"></i> ACC
                                                </button>

                                                <button type="button" class="btn-primary"
                                                    style="flex: 1; background: var(--danger); padding: 8px; font-size: 13px; border: none; border-radius: 5px; color: white; cursor: pointer;"
                                                    onclick="prosesResep({{ $item->id }}, '{{ route('staff.prescriptions.reject', $item->id) }}', 'DELETE', 'Tolak')">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
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
    </div>
@endsection