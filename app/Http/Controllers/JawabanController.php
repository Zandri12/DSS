<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jawaban;
use App\Models\Siswa;
use App\Models\Kriteria;

class JawabanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validasi data (optional tapi sangat direkomendasikan)
    $validated = $request->validate([
        'siswa_id' => 'exists:siswa,id',
        'jawaban' => 'array', // Validasi jika jawaban adalah array
        'jawaban.*.kriteria_id' => 'exists:kriteria,id', // Validasi setiap kriteria_id dalam array jawaban
        'jawaban.*.nilai' => 'numeric', // Validasi setiap nilai dalam array jawaban
    ]);
    

    // Simpan jawaban
    Jawaban::create($validated);

    return response()->json(['message' => 'Jawaban disimpan.']);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
