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
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kriteria', 100);
            $table->decimal('bobot', 3, 2);
            $table->enum('tipe', ['Benefit', 'Cost']);
            $table->text('deskripsi'); // Menambahkan kolom deskripsi
            $table->json('pilihan_dropdown')->nullable();
            $table->enum('tipe_form', ['input', 'select', 'textarea']); // Menambahkan kolom tipe_form
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};