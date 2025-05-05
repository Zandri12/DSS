<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::all();
        return response()->json($kriteria);
    }

    public function create()
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
            'tipe' => 'required|in:benefit,cost',
        ]);

        Kriteria::create($validated);

        return response()->json(['message' => 'Kriteria berhasil disimpan'], 201);
}

    public function show(Kriteria $kriteria)
    {
        return response()->json($kriteria);
    }

    public function edit(Kriteria $kriteria)
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
            'tipe' => 'required|in:benefit,cost',
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update($validated);

        return response()->json([
            'message' => 'Kriteria berhasil diperbarui',
            'data' => $kriteria,
        ]);
    }



    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return response()->json(['message' => 'Berhasil dihapus']);
    }
}