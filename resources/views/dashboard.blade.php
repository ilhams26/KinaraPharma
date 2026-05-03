@extends('layouts.app')

@section('content')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="cards dashboard-cards">
    @if($role === 'admin')
      <div class="card" style="border-left-color: var(--success);">
        <i class="fas fa-wallet text-success"></i>
        <p>Pendapatan Bulan Ini</p>
        <h3 class="text-success">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--primary);">
        <i class="fas fa-pills text-primary"></i>
        <p>Total Obat Real</p>
        <h3 class="text-primary">{{ $totalObat }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--info);">
        <i class="fas fa-shopping-bag text-info"></i>
        <p>Pesanan Selesai</p>
        <h3 class="text-info">{{ $pesananSelesaiBulanIni ?? 0 }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--warning);">
        <i class="fas fa-exclamation-triangle text-warning"></i>
        <p>Obat Menipis</p>
        <h3 class="text-warning">{{ $obatMenipisCount }}</h3>
      </div>
    @else
      <div class="card" style="border-left-color: var(--primary);">
        <i class="fas fa-pills"></i>
        <p>Jumlah Obat</p>
        <h3>{{ $totalObat }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--info);">
        <i class="fas fa-clipboard-list text-info"></i>
        <p>Antrean Pesanan</p>
        <h3 class="text-info">{{ $antreanPesanan ?? 0 }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--success);">
        <i class="fas fa-arrow-up text-success"></i>
        <p>Obat Masuk</p>
        <h3 class="text-success">{{ $obatMasuk ?? 0 }}</h3>
      </div>
      <div class="card" style="border-left-color: var(--warning);">
        <i class="fas fa-exclamation-triangle text-warning"></i>
        <p>Obat Menipis</p>
        <h3 class="text-warning">{{ $obatMenipisCount }}</h3>
      </div>
    @endif
  </div>

  <div class="dashboard-content">

    <div class="chart" style="flex: 2;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h4 style="margin: 0;">Grafik Transaksi</h4>

        <div style="display: flex; gap: 10px; align-items: center;">
          <input type="date" id="startDate"
            style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; outline: none;">
          <span style="font-size: 12px; color: #888;">-</span>
          <input type="date" id="endDate"
            style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; outline: none;">
          <button onclick="updateChart()"
            style="padding: 5px 12px; background: var(--primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">Filter</button>
        </div>
      </div>

      <div style="height: 250px; position: relative;">
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <div class="notif" style="flex: 1; display: flex; flex-direction: column;">
      @if($role === 'admin')
        <h4 style="margin-bottom: 15px;">Metode Pembayaran</h4>
        <div class="notif-body"
          style="flex: 1; display: flex; justify-content: center; align-items: center; min-height: 200px;">
          <canvas id="paymentPieChart"></canvas>
        </div>
      @else
        <h4>Notifikasi</h4>
        <div class="notif-body" style="flex: 1; overflow-y: auto; padding-right: 5px;">
          <ul style="list-style: none; padding: 0; margin: 0;">

            @foreach($notifKadaluarsa ?? [] as $item)
              <li style="margin-bottom: 12px; font-size: 13px;">
                <i class="fas fa-circle" style="color: var(--danger); font-size:10px; margin-right:5px;"></i>
                {{ $item->nama }} <span style="color: var(--danger); font-weight: bold; " > ({{ $item->sisa_hari }} hari)</span>
              </li>
            @endforeach

            @foreach($notifMenipis ?? [] as $item)
              <li style="margin-bottom: 12px; font-size: 13px;">
                <i class="fas fa-circle" style="color: var(--warning); font-size:10px; margin-right:5px;"></i>
                {{ $item->nama }} <span style="color: var(--warning); font-weight: bold;" > (Sisa
                  {{ $item->stok_total }})</span>
              </li>
            @endforeach

            @if(($notifKadaluarsa ?? collect())->isEmpty() && ($notifMenipis ?? collect())->isEmpty())
              <li style="text-align: center; color: #888; margin-top: 30px;">
                <i class="fas fa-bell-slash" style="font-size: 24px; margin-bottom: 10px;"></i><br>
                Belum ada notifikasi
              </li>
            @endif
          </ul>
        </div>
      @endif
    </div>

  </div>

  <script>
    let salesChartInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
      // 1. SET FILTER DEFAULT KE 7 HARI TERAKHIR
      let today = new Date();
      let lastWeek = new Date();
      lastWeek.setDate(today.getDate() - 6);

      document.getElementById('endDate').value = today.toISOString().split('T')[0];
      document.getElementById('startDate').value = lastWeek.toISOString().split('T')[0];

      // 2. RENDER GRAFIK GARIS (TRANSAKSI)
      renderSalesChart();

      // 3. RENDER DIAGRAM LINGKARAN (JIKA ROLE ADMIN)
      let isRoleAdmin = "{{ $role }}" === 'admin';
      if (isRoleAdmin) {
        renderPieChart();
      }
    });

    function renderSalesChart() {
      const ctx = document.getElementById('salesChart').getContext('2d');
      if (salesChartInstance) { salesChartInstance.destroy(); }

      salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
          datasets: [{
            label: 'Transaksi',
            data: [15, 22, 18, 30, 25, 40, 35],
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#3498db',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true },
            x: { grid: { display: false } }
          }
        }
      });
    }

    function renderPieChart() {
      const ctxPie = document.getElementById('paymentPieChart').getContext('2d');

      let cashData = {{ $persenCash ?? 0 }};
      let cashlessData = {{ $persenCashless ?? 0 }};

      new Chart(ctxPie, {
        type: 'doughnut',
        data: {
          labels: ['Cash (Tunai)', 'Cashless (QRIS/Transfer)'],
          datasets: [{
            data: [cashData, cashlessData],
            backgroundColor: ['#2ecc71', '#3498db'],
            borderWidth: 0,
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
          }
        }
      });
    }

    function updateChart() {
      let start = document.getElementById('startDate').value;
      let end = document.getElementById('endDate').value;

      if (!start || !end) {
        if (typeof showNotification === 'function') {
          showNotification('Pilih rentang tanggal terlebih dahulu!', 'error');
        } else { alert('Pilih rentang tanggal terlebih dahulu!'); }
        return;
      }

      if (typeof showNotification === 'function') {
        showNotification('Memfilter data transaksi...', 'success');
      }

      renderSalesChart();
    }
  </script>
@endsection