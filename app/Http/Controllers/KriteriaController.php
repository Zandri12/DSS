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

    public function update(Request $request, Kriteria $kriteria)
    {
        $data = $request->validate([
            'nama' => 'required',
            'bobot' => 'required|numeric',
            'tipe' => 'required',
        ]);
        $kriteria->update($data);
        return response()->json($kriteria);
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return response()->json(null, 204);
    }
}