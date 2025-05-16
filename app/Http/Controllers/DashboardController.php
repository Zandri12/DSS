<?php

namespace App\Http\Controllers;


use App\Models\Jawaban;
use App\Models\Siswa;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Controller DashboardController.php
    public function index(Request $request)
{
    $from = $request->input('from');
    $to = $request->input('to');

    $query = Jawaban::with('siswa', 'kriteria');

    // Jika filter tanggal tersedia, lakukan penyaringan berdasarkan created_at
    if ($from && $to) {
        $startDate = Carbon::parse($from)->startOfDay();
        $endDate = Carbon::parse($to)->endOfDay();

        Log::info("Filter tanggal diterapkan: {$startDate} - {$endDate}");

        $query->whereBetween('created_at', [$startDate, $endDate]);
    } else {
        Log::info("Filter tanggal tidak diterapkan. Menampilkan semua data.");
    }

    // Ambil dan transformasi data jawaban untuk grafik
    $grafikData = $query->get()->map(function ($jawaban) {
        return [
            'nama_siswa' => $jawaban->siswa->nama_siswa ?? 'Tidak diketahui',
            'nama_kriteria' => $jawaban->kriteria->nama_kriteria ?? 'Tidak diketahui',
            'jawaban' => $jawaban->jawaban,
        ];
    });

    // Ambil data status siswa untuk chart Pie
    $statusData = Siswa::select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get();

    // Kirim data ke frontend
    return Inertia::render('Dashboard', [
        'grafikData' => $grafikData,
        'statusData' => $statusData,
        'filters' => [
            'from' => $from,
            'to' => $to,
        ],
    ]);
}


}
