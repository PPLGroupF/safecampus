<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(Request $request): View
    {
        // Ringkasan hanya dihitung dari laporan milik mahasiswa yang sedang login.
        $counts = $request->user()->reports()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $summary = [
            'total' => $counts->sum(),
            'verifikasi' => $counts->only([Report::STATUS_DIAJUKAN, Report::STATUS_DALAM_VERIFIKASI, Report::STATUS_PERLU_INFORMASI])->sum(),
            'penanganan' => $counts->get(Report::STATUS_DALAM_PENANGANAN, 0),
            'selesai' => $counts->get(Report::STATUS_SELESAI, 0),
        ];

        $latest = $request->user()->reports()->with('category')->latest()->limit(5)->get();

        return view('mahasiswa.beranda', compact('summary', 'latest'));
    }

    public function panduan(): View
    {
        return view('mahasiswa.panduan');
    }
}
