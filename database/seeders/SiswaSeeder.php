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
                'nama_siswa' => 'Andika Pratama',
                'nisn' => '1234567890',
                'kelas' => '9',
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
                'nama_siswa' => 'Salsabila Nuraini',
                'nisn' => '0987654321',
                'kelas' => '9',
                'alamat' => 'Jl. Melati No. 2',
                'penghasilan' => 1000000.00,
                'prestasi' => 'Juara 2 Lomba Sains',
                'nilai_rapor' => 88.75,
                'status' => 'Diproses',
                'tanggungan' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_siswa' => 'Rizky Maulana',
                'nisn' => '1122334455',
                'kelas' => '8',
                'alamat' => 'Jl. Anggrek No. 3',
                'penghasilan' => 1200000.00,
                'prestasi' => 'Juara 3 Futsal Antar Sekolah',
                'nilai_rapor' => 85.60,
                'status' => 'Diproses',
                'tanggungan' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_siswa' => 'Dewi Ayu Lestari',
                'nisn' => '6677889900',
                'kelas' => '8',
                'alamat' => 'Jl. Kenanga No. 4',
                'penghasilan' => 950000.00,
                'prestasi' => 'Juara 1 Lomba Pidato',
                'nilai_rapor' => 90.00,
                'status' => 'Diproses',
                'tanggungan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_siswa' => 'Fajar Ramadhan',
                'nisn' => '5544332211',
                'kelas' => '7',
                'alamat' => 'Jl. Dahlia No. 5',
                'penghasilan' => 1300000.00,
                'prestasi' => 'Juara Harapan 1 Lomba Cerdas Cermat',
                'nilai_rapor' => 89.20,
                'status' => 'Diproses',
                'tanggungan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
