<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Obat;
use App\Models\Batch;
use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pembeli = User::create(['username' => 'ilham', 'no_hp' => '081211111113', 'password' => Hash::make('123'), 'role' => 'pembeli']);
        $pembeli = User::create(['username' => 'ghazali', 'no_hp' => '081265398468', 'password' => Hash::make('123'), 'role' => 'pembeli']);
        User::create(['username' => 'admin', 'no_hp' => '081211111111', 'password' => Hash::make('123'), 'role' => 'admin']);
        User::create(['username' => 'staff', 'no_hp' => '081211111112', 'password' => Hash::make('123'), 'role' => 'staff']);

        $obatBebas = Kategori::create(['nama' => 'Obat Bebas']);
        $obatKeras = Kategori::create(['nama' => 'Obat Keras']);
        $vitamin = Kategori::create(['nama' => 'Vitamin']);
        $alatKesehatan = Kategori::create(['nama' => 'Alat Kesehatan']);

        $obats = [
            ['kategori' => $obatBebas, 'nama' => 'Paracetamol 500mg', 'deskripsi' => 'Obat penurun demam', 'harga' => 5000, 'jenis' => 'biasa', 'stok_minimum' => 50],
            ['kategori' => $obatBebas, 'nama' => 'OBH Combi', 'deskripsi' => 'Obat batuk flu', 'harga' => 12000, 'jenis' => 'biasa', 'stok_minimum' => 30],
            ['kategori' => $obatBebas, 'nama' => 'Bodrex', 'deskripsi' => 'Obat sakit kepala', 'harga' => 3000, 'jenis' => 'biasa', 'stok_minimum' => 100],
            ['kategori' => $obatBebas, 'nama' => 'Promag', 'deskripsi' => 'Obat maag', 'harga' => 8000, 'jenis' => 'biasa', 'stok_minimum' => 40],
            ['kategori' => $vitamin, 'nama' => 'Vitamin C 1000mg', 'deskripsi' => 'Suplemen vitamin', 'harga' => 25000, 'jenis' => 'biasa', 'stok_minimum' => 20],
        ];

        foreach ($obats as $data) {
            $obat = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum'],
                'foto' => null
            ]);
            Batch::create([
                'obat_id' => $obat->id,
                'batch_number' => 'BATCH-' . strtoupper(Str::random(5)),
                'expired_date' => now()->addYears(2),
                'jumlah_awal' => 500,
                'jumlah_sisa' => 500
            ]);
        }

        $obatKerasData = [
            ['kategori' => $obatKeras, 'nama' => 'Amoxicillin 500mg', 'deskripsi' => 'Antibiotik infeksi', 'harga' => 15000, 'jenis' => 'keras', 'stok_minimum' => 30],
            ['kategori' => $obatKeras, 'nama' => 'Alprazolam 0.5mg', 'deskripsi' => 'Obat anti-anxiety', 'harga' => 50000, 'jenis' => 'keras', 'stok_minimum' => 10],
        ];

        $lastObatKeras = null;
        foreach ($obatKerasData as $data) {
            $lastObatKeras = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum'],
                'foto' => null
            ]);
            Batch::create([
                'obat_id' => $lastObatKeras->id,
                'batch_number' => 'BATCH-' . strtoupper(Str::random(5)),
                'expired_date' => now()->addYears(2),
                'jumlah_awal' => 200,
                'jumlah_sisa' => 200
            ]);
        }

        Prescription::create([
            'user_id' => $pembeli->id,
            'obat_id' => $lastObatKeras->id,
            'foto_resep' => 'test.jpg',
            'status' => 'menunggu'
        ]);

        Order::create([
            'user_id' => $pembeli->id,
            'order_code' => 'ORD-' . time() . '1',
            'metode_pembayaran' => 'midtrans',
            'total_harga' => 25000,
            'status' => 'diproses',
            'payment_status' => 'paid'
        ]);

        Order::create([
            'user_id' => $pembeli->id,
            'order_code' => 'ORD-' . time() . '2',
            'metode_pembayaran' => 'midtrans',
            'total_harga' => 50000,
            'status' => 'diproses',
            'payment_status' => 'unpaid'
        ]);
    }
}
