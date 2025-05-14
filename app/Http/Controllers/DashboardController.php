<?php

namespace App\Http\Controllers;


use App\Models\Jawaban;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $data = DB::table('jawaban')
        ->join('siswa', 'jawaban.siswa_id', '=', 'siswa.id')
        ->join('kriteria', 'jawaban.kriteria_id', '=', 'kriteria.id')
        ->select('siswa.nama_siswa', 'kriteria.nama_kriteria', 'jawaban.jawaban')
        ->get();

    // Data status siswa untuk Pie Chart
    $statusData = DB::table('siswa')
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get();

    return Inertia::render('Dashboard', [
        'grafikData' => $data,
        'statusData' => $statusData,
    ]);
}

}
