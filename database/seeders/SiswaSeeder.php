<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('siswa')->insert([
            [
                'nama_siswa' => 'Siswa Satu',
                'nisn' => '1234567890',
                'kelas' => 'XII IPA 1',
                'alamat' => 'Jl. Mawar No. 1',
                'penghasilan' => 1500000.00,
                'prestasi' => 'Juara 1 Olimpiade Matematika',
                'nilai_rapor' => 92.50,
                'status' => 'Diproses',
                'tanggungan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_siswa' => 'Siswa Dua',
                'nisn' => '0987654321',
                'kelas' => 'XII IPA 2',
                'alamat' => 'Jl. Melati No. 2',
                'penghasilan' => 1000000.00,
                'prestasi' => 'Juara 2 Lomba Sains',
                'nilai_rapor' => 88.75,
                'status' => 'Diproses',
                'tanggungan' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}