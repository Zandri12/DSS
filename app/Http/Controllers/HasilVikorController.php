<?php

namespace App\Http\Controllers;

use App\Models\HasilVikor;
use Illuminate\Http\Request;

class HasilVikorController extends Controller
{
    public function index()
    {
        $hasil = HasilVikor::all();
        return response()->json($hasil);
    }

    public function create()
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|integer',
            'nilai_vikor' => 'required|numeric',
            'peringkat' => 'required|integer',
        ]);
        $hasil = HasilVikor::create($data);
        return response()->json($hasil, 201);
    }

    public function show(HasilVikor $hasilVikor)
    {
        return response()->json($hasilVikor);
    }

    public function edit(HasilVikor $hasilVikor)
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function update(Request $request, HasilVikor $hasilVikor)
    {
        $data = $request->validate([
            'siswa_id' => 'required|integer',
            'nilai_vikor' => 'required|numeric',
            'peringkat' => 'required|integer',
        ]);
        $hasilVikor->update($data);
        return response()->json($hasilVikor);
    }

    public function destroy(HasilVikor $hasilVikor)
    {
        $hasilVikor->delete();
        return response()->json(null, 204);
    }
}