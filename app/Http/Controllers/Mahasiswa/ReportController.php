<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Services\AuditLogger;
use App\Support\TicketCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(): View
    {
        return view('reports.create', [
            'report' => new Report(),
            'categories' => Category::optionsForReport(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $report = DB::transaction(function () use ($data, $request) {
            $report = Report::create($data + [
                'user_id' => $request->user()->id,
                'kode_tiket' => TicketCode::generate(),
                'status' => Report::STATUS_DIAJUKAN,
            ]);

            // Status awal dicatat oleh sistem.
            $report->statuses()->create([
                'status_baru' => Report::STATUS_DIAJUKAN,
                'catatan' => 'Laporan berhasil dibuat dan menunggu verifikasi petugas.',
            ]);

            return $report;
        });

        AuditLogger::record('Mengajukan laporan', 'laporan', $report->kode_tiket, anonymous: $report->isAnonymous());

        return redirect()->route('laporan.create')
            ->with('success', 'Laporan berhasil dikirim. Kode tiket Anda: '.$report->kode_tiket);
    }

    private function validated(Request $request, ?Report $report = null): array
    {
        return $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id'),
            ],
            'kronologi' => ['required', 'string', 'min:30', 'max:5000'],
            'terlapor_peran' => ['required', Rule::in(array_keys(Report::TERLAPOR_PERAN))],
            'terlapor_keterangan' => ['nullable', 'string', 'max:1000'],
            'mode_identitas' => ['required', Rule::in(array_keys(Report::MODES))],
        ]);
    }
}
