<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilVikor extends Model
{
    use HasFactory;
    protected $fillable = [
        'siswa_id', 'nilai_q', 'status', 'ranking'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}