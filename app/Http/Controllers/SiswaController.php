<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request, )
    {
        $siswa = Siswa::all();
        if ($request->wantsJson()) {
            return response()->json($siswa);
        }
        return Inertia::render('students/StudentDatas', [
            'siswa' => $siswa
        ]);
    }

    public function create()
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'nis' => 'required',
            'alamat' => 'nullable',
            'kelas' => 'nullable',
        ]);
        $siswa = Siswa::create($data);
        return response()->json($siswa, 201);
    }

    public function show(Siswa $siswa)
    {
        return response()->json($siswa);
    }

    public function edit(Siswa $siswa)
    {
        // Untuk Inertia atau view jika diperlukan
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'nama' => 'required',
            'nis' => 'required',
            'alamat' => 'nullable',
            'kelas' => 'nullable',
        ]);
        $siswa->update($data);
        return response()->json($siswa);
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return response()->json(null, 204);
    }
}