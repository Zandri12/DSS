<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jawaban;
use App\Models\Siswa;
use App\Models\Kriteria;
use DB;

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
    $validated = $request->validate([
        'siswa_id' => 'required|exists:siswa,id',
        'jawaban' => 'required|array',
        'jawaban.*.kriteria_id' => 'required|exists:kriteria,id',
        'jawaban.*.nilai' => 'required|numeric',
    ]);

    DB::beginTransaction();
    try {
        foreach ($validated['jawaban'] as $item) {
            Jawaban::create([
                'siswa_id' => $validated['siswa_id'],
                'kriteria_id' => $item['kriteria_id'],
                'jawaban' => $item['nilai'], // kolom `jawaban` diisi dari nilai
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Jawaban berhasil disimpan']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Gagal menyimpan jawaban',
            'error' => $e->getMessage(),
        ], 500);
    }
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
