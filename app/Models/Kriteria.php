<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    protected $table = 'kriteria';
    protected $fillable = [
        'nama_kriteria',
        'bobot',
        'tipe',
        'tipe_form',
        'deskripsi',
        'pilihan_dropdown',
    ];

    protected $casts = [
        'pilihan_dropdown' => 'array'
    ];
}