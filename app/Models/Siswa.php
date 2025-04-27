<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $fillable = [
        'nama_siswa', 'nisn', 'kelas', 'alamat', 'penghasilan', 'prestasi', 'nilai_rapor', 'status', 'tanggungan'
    ];
}