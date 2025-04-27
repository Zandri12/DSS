<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HasilVikorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hasil_vikor')->insert([
            [
                'siswa_id' => 1,
                'nilai_q' => 0.123,
                'status' => 'Diterima',
                'ranking' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'siswa_id' => 2,
                'nilai_q' => 0.456,
                'status' => 'Tidak Diterima',
                'ranking' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}