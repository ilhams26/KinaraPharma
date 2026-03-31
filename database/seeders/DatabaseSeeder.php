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
        User::create([
            'username' => 'admin',
            'no_hp' => '081211111110',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'staff',
            'no_hp' => '081211111111',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'username' => 'ilham',
            'no_hp' => '081222222222',
            'password' => null, 
            'role' => 'pembeli',
        ]);

        // 2.  Kategori Obat
        $demam = Kategori::create(['nama' => 'Demam']);
        $batuk = Kategori::create(['nama' => 'Batuk & Flu']);
        $sakit_kepala = Kategori::create(['nama' => 'Sakit Kepala']);
        $vitamin = Kategori::create(['nama' => 'Vitamin']);
        $maag = Kategori::create(['nama' => 'Maag']);

        $obats = [
            [
                'kategori' => $demam,
                'nama' => 'Paracetamol 500mg',
                'deskripsi' => 'Obat penurun demam dan pereda nyeri',
                'harga' => 5000,
                'jenis' => 'biasa',
                'stok_minimum' => 50,
            ],
            [
                'kategori' => $batuk,
                'nama' => 'OBH Combi',
                'deskripsi' => 'Obat batuk flu',
                'harga' => 12000,
                'jenis' => 'biasa',
                'stok_minimum' => 30,
            ],
            [
                'kategori' => $sakit_kepala,
                'nama' => 'Bodrex',
                'deskripsi' => 'Obat sakit kepala',
                'harga' => 3000,
                'jenis' => 'biasa',
                'stok_minimum' => 100,
            ],
            [
                'kategori' => $vitamin,
                'nama' => 'Vitamin C 1000mg',
                'deskripsi' => 'Suplemen vitamin C',
                'harga' => 25000,
                'jenis' => 'biasa',
                'stok_minimum' => 20,
            ],
            [
                'kategori' => $maag,
                'nama' => 'Promag',
                'deskripsi' => 'Obat maag',
                'harga' => 8000,
                'jenis' => 'biasa',
                'stok_minimum' => 40,
            ],
        ];

        foreach ($obats as $data) {
            $obat = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum'],
            ]);

            // Create batch stok awal
            Batch::create([
                'obat_id' => $obat->id,
                'batch_number' => 'BATCH-' . str_pad($obat->id, 3, '0', STR_PAD_LEFT),
                'expired_date' => now()->addYears(2),
                'jumlah_awal' => 500,
                'jumlah_sisa' => 500,
            ]);
        }

        // 4. Data Obat Keras (Resep)
        $obatKeras = [
            [
                'kategori' => $demam,
                'nama' => 'Amoxicillin 500mg',
                'deskripsi' => 'Antibiotik untuk infeksi bakteri',
                'harga' => 15000,
                'jenis' => 'keras',
                'stok_minimum' => 30,
            ],
            [
                'kategori' => $sakit_kepala,
                'nama' => 'Alprazolam 0.5mg',
                'deskripsi' => 'Obat penenang resep dokter',
                'harga' => 50000,
                'jenis' => 'keras',
                'stok_minimum' => 10,
            ],
        ];

        foreach ($obatKeras as $data) {
            $obat = Obat::create([
                'kategori_id' => $data['kategori']->id,
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'harga' => $data['harga'],
                'jenis' => $data['jenis'],
                'stok_minimum' => $data['stok_minimum'],
            ]);

            Batch::create([
                'obat_id' => $obat->id,
                'batch_number' => 'BATCH-' . str_pad($obat->id, 3, '0', STR_PAD_LEFT),
                'expired_date' => now()->addYears(2),
                'jumlah_awal' => 200,
                'jumlah_sisa' => 200,
            ]);
        }

        $this->command->info('✅ Database berhasil diisi dengan data Kinara Pharma!');
        $this->command->info('👤 Login Admin: 081211111110 / password');
        $this->command->info('👤 Login Staff: 081211111111 / password');
        $this->command->info('👤 Login Pembeli (Mobile): 081222222222 / (Pakai OTP)');
    }
}