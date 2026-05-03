@extends('layouts.app')

@section('content')
    <div>

        <div class="dashboard-content">
            <div class="table-section" style="flex: 2;">
                <div class="table-header">
                    <h2 style="color: var(--primary-hover);">Katalog Obat</h2>
                    <input type="text" id="searchObat" placeholder="Cari nama obat..."
                        style="padding: 8px; border: 1px solid var(--primary); border-radius: 5px; width: 200px;"
                        onkeyup="filterObat()">
                </div>

                <div class="cards" id="katalogObat"
                    style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                    @forelse($obats as $obat)
                        <div class="card obat-item"
                            style="cursor: pointer; padding: 15px 10px; border-left: 4px solid var(--primary);"
                            onclick="addToCart({{ $obat->id }}, '{{ addslashes($obat->nama) }}', {{ (float) $obat->harga }})">
                            <i class="fas fa-pills" style="font-size: 20px;"></i>
                            <p class="obat-nama" style="font-weight: bold; font-size: 14px; margin: 5px 0; color: var(--text);">
                                {{ $obat->nama }}</p>
                            <h3 style="font-size: 16px; color: var(--success);">Rp
                                {{ number_format((float) $obat->harga, 0, ',', '.') }}</h3>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 20px;">
                            <i class="fas fa-box-open" style="font-size: 30px; margin-bottom: 10px;"></i>
                            <p>Belum ada obat yang memiliki stok sisa.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="notif" style="flex: 1; display: flex; flex-direction: column;">
                <h4 style="text-align: center;"><i class="fas fa-shopping-cart"></i> Keranjang</h4>

                <div class="notif-body" style="flex: 1; overflow-y: auto; padding-right: 5px; min-height: 250px;"
                    id="cartItems">
                    <div style="text-align: center; color: var(--text-muted); margin-top: 50px;">Keranjang Kosong</div>
                </div>

                <div style="border-top: 2px dashed #ccc; padding-top: 15px; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-weight: bold;">Total:</span>
                        <span style="font-weight: bold; color: var(--primary-hover); font-size: 20px;" id="cartTotal">Rp 0</span>
                    </div>

                    <form action="{{ route('staff.kasir.checkout') }}" method="POST" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="items" id="itemsInput">

                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 14px; font-weight: bold;">Uang Diterima (Rp)</label>
                            <input type="number" name="pembayaran" id="pembayaranInput" required
                                style="width: 100%; padding: 10px; border: 1px solid var(--primary); border-radius: 6px; font-size: 16px;">
                        </div>

                        <button type="button" class="btn-primary"
                            style="width: 100%; justify-content: center; font-size: 16px;" onclick="processCheckout()">
                            Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection