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
        $validated = $request->validate([
            'nama_siswa' => 'required|string',
            'nisn' => 'required|string',
            'kelas' => 'required|string',
            'alamat' => 'required|string',
            'penghasilan' => 'required|numeric',
            'prestasi' => 'nullable|string',
            'nilai_rapor' => 'required|numeric',
            'status' => 'required|string',
            'tanggungan' => 'required|numeric',
        ]);

        Siswa::create($validated);

        return response()->json(['message' => 'Data siswa berhasil disimpan!']);
    }

    public function show(Siswa $siswa)
    {
        return response()->json($siswa);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_siswa' => 'required|string',
            'nisn' => 'required|string',
            'kelas' => 'required|string',
            'alamat' => 'required|string',
            'penghasilan' => 'required|numeric',
            'prestasi' => 'nullable|string',
            'nilai_rapor' => 'required|numeric',
            'status' => 'required|string',
            'tanggungan' => 'required|numeric',
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update($validated);

        return response()->json(['message' => 'Data siswa berhasil diupdate!']);
    }
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
    
        return response()->json(['message' => 'Data siswa berhasil dihapus!']);
    }

    public function batchDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids)) {
            return response()->json(['message' => 'ID tidak valid.'], 400);
        }

        Siswa::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}