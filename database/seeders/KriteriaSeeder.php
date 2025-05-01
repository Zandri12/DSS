<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kriteria')->insert([
            [
                'nama_kriteria' => 'Akademik',
                'bobot' => 0.30,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Ekonomi',
                'bobot' => 0.25,
                'tipe' => 'Cost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Organisasi',
                'bobot' => 0.15,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Wawancara',
                'bobot' => 0.20,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Rekomendasi',
                'bobot' => 0.10,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
