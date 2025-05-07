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
                'deskripsi' => 'Nilai akademik siswa selama di sekolah.',
                'tipe_form' => 'input',
                'pilihan_dropdown' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Ekonomi',
                'bobot' => 0.25,
                'tipe' => 'Cost',
                'deskripsi' => 'Keadaan ekonomi siswa dan keluarganya.',
                'tipe_form' => 'input',
                'pilihan_dropdown' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Organisasi',
                'bobot' => 0.15,
                'tipe' => 'Benefit',
                'deskripsi' => 'Partisipasi dalam kegiatan organisasi atau ekstrakurikuler.',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode(['Tidak Aktif', 'Kurang Aktif', 'Cukup Aktif', 'Sangat Aktif']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Wawancara',
                'bobot' => 0.20,
                'tipe' => 'Benefit',
                'deskripsi' => 'Hasil wawancara dengan siswa terkait motivasi dan tujuan pendidikan.',
                'tipe_form' => 'textarea',
                'pilihan_dropdown' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Rekomendasi',
                'bobot' => 0.10,
                'tipe' => 'Benefit',
                'deskripsi' => 'Rekomendasi dari guru atau pihak lain yang relevan.',
                'tipe_form' => 'textarea',
                'pilihan_dropdown' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
