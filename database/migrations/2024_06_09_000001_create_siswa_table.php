<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa', 100);
            $table->string('nisn', 20);
            $table->string('kelas', 10);
            $table->text('alamat');
            $table->decimal('penghasilan', 15, 2);
            $table->string('prestasi', 255);
            $table->decimal('nilai_rapor', 5, 2);
            $table->enum('status', ['Diterima', 'Tidak Diterima', 'Diproses']);
            $table->integer('tanggungan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};