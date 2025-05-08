<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'siswa_id',
        'kriteria_id',
        'jawaban',
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke kriteria
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
