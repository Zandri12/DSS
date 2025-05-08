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
                'faktor' => 'Prestasi Akademik',
                'bobot' => 0.30,
                'tipe' => 'Benefit',
                'deskripsi' => 'Bagaimana nilai akademik siswa selama di sekolah?',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode([
                    ['label' => '90 - 100', 'value' => 5],
                    ['label' => '80 - 89',  'value' => 4],
                    ['label' => '70 - 79',  'value' => 3],
                    ['label' => '60 - 69',  'value' => 2],
                    ['label' => '< 60',     'value' => 1],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Ekonomi',
                'faktor' => 'Kondisi Ekonomi',
                'bobot' => 0.25,
                'tipe' => 'Cost',
                'deskripsi' => 'Bagaimana keadaan ekonomi siswa dan keluarganya?',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode([
                    ['label' => 'Sangat Mampu (Gaji > 5 Juta)',     'value' => 1],
                    ['label' => 'Mampu (Gaji 4 - 5 Juta)',          'value' => 2],
                    ['label' => 'Cukup Mampu (Gaji 3 - 4 Juta)',    'value' => 3],
                    ['label' => 'Kurang Mampu (Gaji 2 - 3 Juta)',   'value' => 4],
                    ['label' => 'Tidak Mampu (Gaji < 2 Juta)',      'value' => 5],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Organisasi',
                'faktor' => 'Keaktifan Organisasi',
                'bobot' => 0.15,
                'tipe' => 'Benefit',
                'deskripsi' => 'Seberapa aktif siswa dalam kegiatan organisasi atau ekstrakurikuler?',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode([
                    ['label' => 'Sangat Aktif',       'value' => 5],
                    ['label' => 'Aktif',              'value' => 4],
                    ['label' => 'Cukup Aktif',        'value' => 3],
                    ['label' => 'Kurang Aktif',       'value' => 2],
                    ['label' => 'Tidak Aktif',        'value' => 1],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Wawancara',
                'faktor' => 'Wawancara',
                'bobot' => 0.20,
                'tipe' => 'Benefit',
                'deskripsi' => 'Apa motivasi dan tujuan pendidikan siswa berdasarkan hasil wawancara?',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode([
                    ['label' => '90 - 100', 'value' => 5],
                    ['label' => '80 - 89',  'value' => 4],
                    ['label' => '70 - 79',  'value' => 3],
                    ['label' => '60 - 69',  'value' => 2],
                    ['label' => '< 60',     'value' => 1],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kriteria' => 'Rekomendasi',
                'faktor' => 'Rekomendasi Guru',
                'bobot' => 0.10,
                'tipe' => 'Benefit',
                'deskripsi' => 'Apa isi rekomendasi dari guru atau pihak lain terkait siswa?',
                'tipe_form' => 'select',
                'pilihan_dropdown' => json_encode([
                    ['label' => 'Sangat Direkomendasikan',     'value' => 5],
                    ['label' => 'Direkomendasikan',            'value' => 4],
                    ['label' => 'Cukup Direkomendasikan',      'value' => 3],
                    ['label' => 'Kurang Direkomendasikan',     'value' => 2],
                    ['label' => 'Tidak Direkomendasikan',      'value' => 1],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
