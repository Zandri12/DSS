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
                'nama_kriteria' => 'Penghasilan Orang Tua',
                'bobot' => 0.4,
                'tipe' => 'Cost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Prestasi',
                'bobot' => 0.3,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Nilai Rapor',
                'bobot' => 0.2,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Tanggungan Orang Tua',
                'bobot' => 0.1,
                'tipe' => 'Benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}