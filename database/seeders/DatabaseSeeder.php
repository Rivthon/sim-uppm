<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun aplikasi tidak dibuat melalui seeder. Profil akan dibuat otomatis
        // saat pengguna berhasil masuk menggunakan akun PAS.
    }
}
