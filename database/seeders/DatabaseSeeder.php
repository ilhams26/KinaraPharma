<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Obat;
use App\Models\Batch;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'no_hp' => '081234567890',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);
        User::create([
            'username' => 'staff',
            'no_hp' => '0812345678910',
            'password' => Hash::make('123'),
            'role' => 'staff',
        ]);
        User::create([
            'username' => 'ilham',
            'no_hp' => '081211111113',
            'password' => Hash::make('123'),
            'role' => 'pembeli',
        ]);
    }
}
