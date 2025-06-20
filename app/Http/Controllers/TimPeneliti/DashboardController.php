<?php

namespace App\Http\Controllers\TimPeneliti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jawaban;
use App\Models\HasilAnalisis;

class DashboardController extends Controller
{
    public function index(){
        $pretest = Jawaban::where('jenis_test', 'pretest')->count();
        $posttest = Jawaban::where('jenis_test', 'posttest')->count();
        $riwayatPretest = HasilAnalisis::with('pasien')
            ->whereNotNull('skor_pretest') // hanya data yang sudah diisi
            ->select('id_pasien', 'skor_pretest', 'tanggal_pretest')
            ->latest('tanggal_pretest')
            ->get()
            ->unique('id_pasien')
            ->take(5);

        $riwayatPosttest = HasilAnalisis::with('pasien')
            ->whereNotNull('skor_posttest') // hanya data yang sudah diisi
            ->select('id_pasien', 'skor_posttest', 'tanggal_posttest')
            ->latest('tanggal_posttest')
            ->get()
            ->unique('id_pasien')
            ->take(5);

        $totalAll = HasilAnalisis::whereNotNull('kesimpulan')->count();

        $totalNaik = HasilAnalisis::where('kesimpulan', 'Meningkat')->count();
        $totalTurun = HasilAnalisis::where('kesimpulan', 'Menurun')->count();
        $totalNetral = HasilAnalisis::where('kesimpulan', 'Netral')->count();

        // Hitung persentase
        $persenNaik = $totalAll > 0 ? round(($totalNaik / $totalAll) * 100, 2) : 0;
        $persenTurun = $totalAll > 0 ? round(($totalTurun / $totalAll) * 100, 2) : 0;
        $persenNetral = $totalAll > 0 ? round(($totalNetral / $totalAll) * 100, 2) : 0;


        return view('tim.dashboard', compact(
            'pretest',
            'posttest', 
            'riwayatPretest', 
            'riwayatPosttest', 
            'persenNaik',
            'persenTurun',
            'persenNetral'
        ));
    }
}
