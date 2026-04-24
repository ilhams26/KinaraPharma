<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Obat;
use App\Models\Batch;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        User::create(['username' => 'admin', 'no_hp' => '081211111111', '123' => Hash::make('password'), 'role' => 'admin']);
        User::create(['username' => 'staff', 'no_hp' => '081211111112', '123' => Hash::make('password'), 'role' => 'staff']);
        User::create(['username' => 'pembeli', 'no_hp' => '081211111113', '123' => Hash::make('password'), 'role' => 'pembeli']);
        // 2. Kategori
        $demam = Kategori::create(['nama' => 'Demam']);
        $batuk = Kategori::create(['nama' => 'Batuk & Flu']);
        $sakit_kepala = Kategori::create(['nama' => 'Sakit Kepala']);
        $vitamin = Kategori::create(['nama' => 'Vitamin']);
        $maag = Kategori::create(['nama' => 'Maag']);

        // 3. Obat Biasa
        $obats = [
            ['kategori' => $demam, 'nama' => 'Paracetamol 500mg', 'deskripsi' => 'Obat penurun demam', 'harga' => 5000, 'jenis' => 'biasa', 'stok_minimum' => 50],
            ['kategori' => $batuk, 'nama' => 'OBH Combi', 'deskripsi' => 'Obat batuk flu', 'harga' => 12000, 'jenis' => 'biasa', 'stok_minimum' => 30],
            ['kategori' => $sakit_kepala, 'nama' => 'Bodrex', 'deskripsi' => 'Obat sakit kepala', 'harga' => 3000, 'jenis' => 'biasa', 'stok_minimum' => 100],
            ['kategori' => $vitamin, 'nama' => 'Vitamin C 1000mg', 'deskripsi' => 'Suplemen vitamin', 'harga' => 25000, 'jenis' => 'biasa', 'stok_minimum' => 20],
            ['kategori' => $maag, 'nama' => 'Promag', 'deskripsi' => 'Obat maag', 'harga' => 8000, 'jenis' => 'biasa', 'stok_minimum' => 40],
        ];

        foreach ($obats as $data) {
            $obat = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum']
            ]);
            Batch::create(['obat_id' => $obat->id, 'batch_number' => 'BATCH-' . str_pad($obat->id, 3, '0', STR_PAD_LEFT), 'expired_date' => now()->addYears(2), 'jumlah_awal' => 500, 'jumlah_sisa' => 500]);
        }

        // 4. Obat Keras
        $obatKeras = [
            ['kategori' => $demam, 'nama' => 'Amoxicillin 500mg', 'deskripsi' => 'Antibiotik infeksi', 'harga' => 15000, 'jenis' => 'keras', 'stok_minimum' => 30],
            ['kategori' => $sakit_kepala, 'nama' => 'Alprazolam 0.5mg', 'deskripsi' => 'Obat anti-anxiety', 'harga' => 50000, 'jenis' => 'keras', 'stok_minimum' => 10],
        ];

        foreach ($obatKeras as $data) {
            $obat = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum']
            ]);
            Batch::create(['obat_id' => $obat->id, 'batch_number' => 'BATCH-' . str_pad($obat->id, 3, '0', STR_PAD_LEFT), 'expired_date' => now()->addYears(2), 'jumlah_awal' => 200, 'jumlah_sisa' => 200]);
        }
    }
}
