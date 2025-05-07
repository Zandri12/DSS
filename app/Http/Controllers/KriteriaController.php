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
        // Menambahkan validasi untuk kolom baru
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
            'tipe' => 'required|in:benefit,cost',  // Memperbaiki casing tipe Benefit dan Cost
            'deskripsi' => 'required|string',  // Validasi deskripsi
           'pilihan_dropdown' => 'nullable|array',
            'tipe_form' => 'required|in:input,select,textarea',  // Validasi tipe_form
        ]);

        // Menyimpan data ke dalam tabel kriteria
        $kriteria = Kriteria::create($validated);

        return response()->json(['message' => 'Kriteria berhasil disimpan', 'data' => $kriteria], 201);
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
        // Menambahkan validasi untuk kolom baru
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric',
            'tipe' => 'required|in:cenefit,cost',  // Memperbaiki casing tipe Benefit dan Cost
            'deskripsi' => 'required|string',  // Validasi deskripsi
            'pilihan_dropdown' => 'nullable|array',
            'tipe_form' => 'required|in:input,select,textarea',  // Validasi tipe_form
        ]);

        // Mencari data kriteria berdasarkan ID
        $kriteria = Kriteria::findOrFail($id);
        // Mengupdate data kriteria
        $kriteria->update($validated);

        return response()->json([
            'message' => 'Kriteria berhasil diperbarui',
            'data' => $kriteria,
        ]);
    }

    public function destroy($id)
    {
        // Mencari kriteria berdasarkan ID dan menghapusnya
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return response()->json(['message' => 'Kriteria berhasil dihapus']);
    }
}
