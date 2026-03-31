@extends('layouts.app')

@section('content')

  <div class="cards">
    <div class="card">
      <i class="fas fa-pills"></i>
      <p>Jumlah Obat</p>
      <h3>940</h3>
    </div>

    <div class="card" style="border-left-color: var(--success);">
      <i class="fas fa-check-circle text-success"></i>
      <p>Obat Masuk</p>
      <h3 class="text-success">50</h3>
    </div>

    <div class="card" style="border-left-color: var(--primary-hover);">
      <i class="fas fa-arrow-up text-info"></i>
      <p>Obat Keluar</p>
      <h3 class="text-info">45</h3>
    </div>

    <div class="card" style="border-left-color: var(--warning);">
      <i class="fas fa-exclamation-triangle text-warning"></i>
      <p>Obat Menipis</p>
      <h3 class="text-warning">3</h3>
    </div>
  </div>

  <div class="dashboard-content">

    <div class="chart">
      <h4>Grafik Transaksi</h4>
      <div
        style="height: 250px; background: #f0f4f8; border-radius: 8px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #888;">
        [ Grafik Chart.js Belum di Integrasikan]
      </div>
    </div>

    <div class="notif">
      <h4>Notifikasi</h4>
      <div class="notif-body">
        <ul>
          <li><i class="fas fa-circle" style="color: var(--warning); font-size:10px; margin-right:5px;"></i> Paracetamol
            (20)</li>
          <li><i class="fas fa-circle" style="color: var(--danger); font-size:10px; margin-right:5px;"></i> Amoxicillin (2
            bulan)</li>
          <li><i class="fas fa-circle" style="color: var(--warning); font-size:10px; margin-right:5px;"></i> Vitamin C
            (25)</li>
        </ul>
      </div>
    </div>

  </div>

@endsection