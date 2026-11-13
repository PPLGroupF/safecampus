<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /** Status yang tampil di antrean jika petugas tidak memilih filter status. */
    private const ACTIVE_STATUSES = [
        Report::STATUS_DIAJUKAN,
        Report::STATUS_DALAM_VERIFIKASI,
        Report::STATUS_PERLU_INFORMASI,
        Report::STATUS_DALAM_PENANGANAN,
    ];

    public function queue(Request $request): View
    {
        $base = $this->visibleReports($request);

        $stats = (clone $base)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $reports = (clone $base)
            ->with(['category', 'unit', 'pelapor'])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->status),
                fn ($query) => $query->whereIn('status', self::ACTIVE_STATUSES),
            )
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->category_id))
            ->when($request->filled('q'), fn ($query) => $query->where('kode_tiket', 'like', '%'.$request->q.'%'))
            // Laporan paling lama menunggu ditampilkan lebih dulu.
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.antrean', [
            'reports' => $reports,
            'stats' => $stats,
            'categories' => Category::orderBy('nama_kategori')->get(),
        ]);
    }

    public function show(Request $request, Report $report): View
    {
        // Kronologi dan keterangan terlapor didekripsi otomatis oleh cast "encrypted" pada model.
        $report->load(['category', 'unit.campus', 'statuses', 'pelapor']);

        return view('petugas.show', compact('report'));
    }

    /**
     * Laporan yang boleh dilihat petugas. Laporan yang dibatalkan pelapor tidak ditangani.
     */
    private function visibleReports(Request $request)
    {
        return Report::query()->where('status', '!=', Report::STATUS_DIBATALKAN);
    }
}
