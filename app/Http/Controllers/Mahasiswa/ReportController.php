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
    public function index(Request $request): View
    {
        $reports = $request->user()->reports()
            ->with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('kode_tiket', 'like', '%'.$request->q.'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->category_id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', [
            'reports' => $reports,
            'categories' => Category::orderBy('nama_kategori')->get(),
        ]);
    }

    public function show(Request $request, Report $report): View
    {
        $this->ensureOwner($request, $report);

        $report->load(['category', 'unit', 'statuses']);

        return view('reports.show', compact('report'));
    }

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
                // Unit asal pelapor terisi otomatis dari profil, bukan dari input formulir.
                'unit_id' => $request->user()->unit_id,
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

        return redirect()->route('laporan.show', $report)
            ->with('success', 'Laporan berhasil dikirim. Kode tiket Anda: '.$report->kode_tiket);
    }

    public function edit(Request $request, Report $report): View|RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (! $report->isEditableByReporter()) {
            return $this->lockedResponse($report);
        }

        return view('reports.edit', [
            'report' => $report,
            'categories' => Category::optionsForReport($report->category_id),
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (! $report->isEditableByReporter()) {
            return $this->lockedResponse($report);
        }

        $report->update($this->validated($request, $report));

        AuditLogger::record('Mengubah laporan', 'laporan', $report->kode_tiket, anonymous: $report->isAnonymous());

        return redirect()->route('laporan.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Laporan tidak dihapus dari basis data, hanya diberi status Dibatalkan.
     */
    public function cancel(Request $request, Report $report): RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (! $report->isEditableByReporter()) {
            return $this->lockedResponse($report);
        }

        DB::transaction(function () use ($report) {
            $report->update(['status' => Report::STATUS_DIBATALKAN]);
            $report->statuses()->create([
                // Pembatalan oleh pelapor anonim dicatat tanpa identitas.
                'diubah_oleh' => $report->isAnonymous() ? null : $report->user_id,
                'status_baru' => Report::STATUS_DIBATALKAN,
                'catatan' => 'Laporan dibatalkan oleh pelapor.',
            ]);
        });

        AuditLogger::record('Membatalkan laporan', 'laporan', $report->kode_tiket, anonymous: $report->isAnonymous());

        return redirect()->route('laporan.show', $report)->with('success', 'Laporan telah dibatalkan.');
    }

    private function lockedResponse(Report $report): RedirectResponse
    {
        return redirect()->route('laporan.show', $report)
            ->with('error', 'Laporan sudah diproses petugas sehingga tidak dapat diubah atau dibatalkan.');
    }

    /**
     * Pelapor hanya boleh membuka laporannya sendiri. Laporan milik orang lain
     * dianggap tidak ada (404) agar keberadaannya tidak terungkap.
     */
    private function ensureOwner(Request $request, Report $report): void
    {
        abort_unless($report->user_id === $request->user()->id, 404);
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
