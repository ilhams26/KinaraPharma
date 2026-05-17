<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Obat;
use App\Models\Batch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $admin = User::create([
            'username' => 'admin',
            'no_hp' => '081211111111',
            'password' => Hash::make('123'),
            'role' => 'admin'
        ]);

        $staff = User::create([
            'username' => 'staff',
            'no_hp' => '081211111112',
            'password' => Hash::make('123'),
            'role' => 'staff'
        ]);

        $pembeli = User::create([
            'username' => 'ilham',
            'no_hp' => '081211111113',
            'password' => Hash::make('123'),
            'role' => 'pembeli'
        ]);
        $pembeli = User::create([
            'username' => 'ilham ghazali',
            'no_hp' => '081265398468',
            'password' => Hash::make('123'),
            'role' => 'pembeli'
        ]);

        // KATEGORI
        $obatBebas = Kategori::create([
            'nama' => 'Obat Bebas'
        ]);

        $obatKeras = Kategori::create([
            'nama' => 'Obat Keras'
        ]);

        $vitamin = Kategori::create([
            'nama' => 'Vitamin'
        ]);

        $alat = Kategori::create([
            'nama' => 'Alat Kesehatan'
        ]);

        $obats = [

            [
                'kategori_id' => $obatBebas->id,
                'nama' => 'Paracetamol',
                'deskripsi' => 'Obat penurun demam',
                'harga' => 5000,
                'jenis' => 'bebas',
                'stok_minimum' => 20,
            ],

            [
                'kategori_id' => $obatBebas->id,
                'nama' => 'OBH Combi',
                'deskripsi' => 'Obat batuk dan flu',
                'harga' => 12000,
                'jenis' => 'bebas',
                'stok_minimum' => 15,
            ],

            [
                'kategori_id' => $obatKeras->id,
                'nama' => 'Amoxicillin',
                'deskripsi' => 'Antibiotik',
                'harga' => 18000,
                'jenis' => 'keras',
                'stok_minimum' => 10,
            ],

            [
                'kategori_id' => $obatKeras->id,
                'nama' => 'Alprazolam',
                'deskripsi' => 'Obat anti anxiety',
                'harga' => 50000,
                'jenis' => 'keras',
                'stok_minimum' => 5,
            ],

            [
                'kategori_id' => $vitamin->id,
                'nama' => 'Vitamin C 1000mg',
                'deskripsi' => 'Vitamin daya tahan tubuh',
                'harga' => 25000,
                'jenis' => 'bebas',
                'stok_minimum' => 10,
            ],

            [
                'kategori_id' => $alat->id,
                'nama' => 'Masker Medis',
                'deskripsi' => 'Masker kesehatan',
                'harga' => 15000,
                'jenis' => 'bebas',
                'stok_minimum' => 30,
            ],

        ];

        foreach ($obats as $item) {

            $obat = Obat::create([
                'kategori_id' => $item['kategori_id'],
                'nama' => $item['nama'],
                'deskripsi' => $item['deskripsi'],
                'harga' => $item['harga'],
                'jenis' => $item['jenis'],
                'stok_minimum' => $item['stok_minimum'],
                'gambar' => null,
            ]);

            Batch::create([
                'obat_id' => $obat->id,
                'batch_number' => 'BATCH-' . rand(100, 999),
                'expired_date' => now()->addMonths(rand(6, 24)),
                'jumlah_awal' => rand(50, 200),
                'jumlah_sisa' => rand(20, 150),
            ]);
        }


        $order1 = Order::create([
            'user_id' => $pembeli->id,
            'kode_order' => 'ORD-1001',
            'metode_pembayaran' => 'midtrans',
            'status' => 'Diproses',
            'total_harga' => 30000,
        ]);

        $order2 = Order::create([
            'user_id' => $pembeli->id,
            'kode_order' => 'ORD-1002',
            'metode_pembayaran' => 'midtrans',
            'status' => 'Siap Diambil',
            'total_harga' => 50000,
        ]);

        $order3 = Order::create([
            'user_id' => $pembeli->id,
            'kode_order' => 'ORD-1003',
            'metode_pembayaran' => 'cash',
            'status' => 'Selesai',
            'total_harga' => 15000,
        ]);

        Prescription::create([
            'user_id' => $pembeli->id,
            'status' => 'menunggu',
            'catatan' => 'Mohon validasi resep'
        ]);

        Prescription::create([
            'user_id' => $pembeli->id,
            'status' => 'tervalidasi',
            'catatan' => 'Resep sudah diterima'
        ]);
    }
}
