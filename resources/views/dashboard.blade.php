@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
  <div class="mb-6">
    <h2 class="text-3xl font-bold text-[#1F5F9F]">Dashboard Overview</h2>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-[#2D7BCA] flex flex-col items-center justify-center">
      <span class="text-gray-500 font-semibold text-sm">Jumlah Obat</span>
      <span class="text-3xl font-bold text-[#2D7BCA] mt-2">💊 940</span>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-green-500 flex flex-col items-center justify-center">
      <span class="text-gray-500 font-semibold text-sm">Obat Masuk</span>
      <span class="text-3xl font-bold text-green-500 mt-2">✅ 50</span>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-purple-500 flex flex-col items-center justify-center">
      <span class="text-gray-500 font-semibold text-sm">Obat Keluar</span>
      <span class="text-3xl font-bold text-purple-500 mt-2">↗️ 45</span>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-yellow-500 flex flex-col items-center justify-center">
      <span class="text-gray-500 font-semibold text-sm">Obat Menipis</span>
      <span class="text-3xl font-bold text-yellow-500 mt-2">⚠️ 3</span>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-red-500 flex flex-col items-center justify-center">
      <span class="text-gray-500 font-semibold text-sm">Obat Expired</span>
      <span class="text-3xl font-bold text-red-500 mt-2">❌ 2</span>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="bg-white rounded-xl p-6 shadow-sm col-span-2">
      <h3 class="text-xl font-bold text-[#2D7BCA] mb-4 text-center">Grafik Transaksi</h3>
      <div
        class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
        <span class="text-gray-400">Area Grafik Chart.js (Akan diintegrasikan nanti)</span>
      </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm">
      <h3 class="text-xl font-bold text-[#2D7BCA] mb-4 flex items-center gap-2">
        🔔 Notifikasi
      </h3>
      <ul class="space-y-3 text-gray-700">
        <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Paracetamol sisa 20
        </li>
        <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Amoxicillin expired
          dalam 2 bulan</li>
        <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Vitamin C sisa 25
        </li>
      </ul>
    </div>
  </div>
@endsection