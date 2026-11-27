<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        $this->ensureVisible($request, $report);

        // Laporan baru otomatis masuk ke tahap Dalam Verifikasi saat pertama kali dibuka petugas (diubah oleh sistem).
        if ($report->status === Report::STATUS_DIAJUKAN) {
            $report->changeStatus(Report::STATUS_DALAM_VERIFIKASI, null, 'Laporan diterima sistem dan mulai diperiksa petugas Satgas.');
            AuditLogger::record('Membuka laporan baru (Dalam Verifikasi)', 'verifikasi', $report->kode_tiket);
        }

        // Kronologi dan keterangan terlapor didekripsi otomatis oleh cast "encrypted" pada model.
        $report->load(['category', 'unit.campus', 'statuses', 'pelapor']);

        return view('petugas.show', compact('report'));
    }

    /**
     * Keputusan verifikasi: terima (Dalam Penanganan), minta informasi (Perlu Informasi), atau tolak (Ditolak).
     */
    public function verify(Request $request, Report $report): RedirectResponse
    {
        $this->ensureVisible($request, $report);

        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['terima', 'minta_info', 'tolak'])],
            'catatan' => ['nullable', 'string', 'max:1000', Rule::requiredIf(in_array($request->keputusan, ['minta_info', 'tolak'], true))],
        ], [
            'catatan.required' => 'Alasan atau informasi yang dibutuhkan wajib diisi.',
        ]);

        $status = [
            'terima' => Report::STATUS_DALAM_PENANGANAN,
            'minta_info' => Report::STATUS_PERLU_INFORMASI,
            'tolak' => Report::STATUS_DITOLAK,
        ][$data['keputusan']];

        $this->applyStatus($request, $report, $status, $data['catatan'] ?? null);

        return redirect()->route('petugas.laporan.show', $report)->with('success', 'Keputusan verifikasi disimpan. Status laporan: '.$report->statusLabel().'.');
    }

    /**
     * Perubahan status lanjutan, misalnya Dalam Penanganan ke Selesai.
     */
    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $this->ensureVisible($request, $report);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'catatan' => ['nullable', 'string', 'max:1000', Rule::requiredIf($request->status === Report::STATUS_SELESAI)],
        ], [
            'catatan.required' => 'Ringkasan tindak lanjut wajib diisi saat menutup kasus.',
        ]);

        $this->applyStatus($request, $report, $data['status'], $data['catatan'] ?? null);

        return redirect()->route('petugas.laporan.show', $report)->with('success', 'Status laporan diperbarui menjadi '.$report->statusLabel().'.');
    }

    /**
     * Menugaskan laporan yang belum punya penanggung jawab ke seorang petugas.
     */
    public function assign(Request $request, Report $report): RedirectResponse
    {
        $this->ensureVisible($request, $report);
        $this->ensureAssignable($report);

        if ($report->petugas_id) {
            return back()->with('error', 'Laporan sudah memiliki petugas penanggung jawab. Gunakan Alihkan Penugasan.');
        }

        $petugas = $this->validatedOfficer($request);
        $report->assignTo($petugas, $request->user());

        return $this->redirectAfterAssignment($request, $report, 'Laporan ditugaskan ke '.$petugas->nama.'.');
    }

    /**
     * Mengalihkan penugasan ke petugas lain. Alasan wajib diisi dan tersimpan di riwayat penugasan.
     */
    public function reassign(Request $request, Report $report): RedirectResponse
    {
        $this->ensureVisible($request, $report);
        $this->ensureAssignable($report);

        if (! $report->petugas_id) {
            return back()->with('error', 'Laporan belum ditugaskan. Gunakan Tugaskan.');
        }

        $petugas = $this->validatedOfficer($request, requireReason: true);

        if ($petugas->id === $report->petugas_id) {
            throw ValidationException::withMessages(['petugas_id' => 'Pilih petugas yang berbeda dari penanggung jawab saat ini.']);
        }

        $report->assignTo($petugas, $request->user(), $request->input('alasan'));

        return $this->redirectAfterAssignment($request, $report, 'Penugasan dialihkan ke '.$petugas->nama.'.');
    }

    /**
     * Jika laporan ditugaskan ke petugas lain, petugas ini tidak lagi berhak membukanya,
     * jadi kembali ke antrean. Jika ditugaskan ke diri sendiri, tetap di halaman detail.
     */
    private function redirectAfterAssignment(Request $request, Report $report, string $message): RedirectResponse
    {
        $target = $report->petugas_id === $request->user()->id
            ? route('petugas.laporan.show', $report)
            : route('petugas.antrean');

        return redirect()->to($target)->with('success', $message);
    }

    /**
     * Mencabut penugasan: laporan kembali ke antrean bersama sehingga semua petugas dapat melihatnya lagi.
     * Dipakai misalnya saat petugas penanggung jawab memiliki konflik kepentingan.
     */
    public function unassign(Request $request, Report $report): RedirectResponse
    {
        $this->ensureVisible($request, $report);
        $this->ensureAssignable($report);

        if (! $report->petugas_id) {
            return back()->with('error', 'Laporan belum ditugaskan ke petugas mana pun.');
        }

        $report->revokeAssignment($request->user());

        return redirect()->route('petugas.laporan.show', $report)->with('success', 'Penugasan dicabut. Laporan kembali ke antrean bersama.');
    }

    private function validatedOfficer(Request $request, bool $requireReason = false): User
    {
        $data = $request->validate([
            'petugas_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)->where('is_active', true)],
            'alasan' => [$requireReason ? 'required' : 'nullable', 'string', 'max:255'],
        ], [
            'petugas_id.required' => 'Pilih petugas yang akan ditugaskan.',
            'petugas_id.exists' => 'Petugas yang dipilih tidak valid atau tidak aktif.',
            'alasan.required' => 'Alasan pengalihan wajib diisi.',
        ]);

        return User::findOrFail($data['petugas_id']);
    }

    /**
     * Laporan berstatus akhir tidak bisa ditugaskan lagi.
     */
    private function ensureAssignable(Report $report): void
    {
        if (in_array($report->status, [Report::STATUS_DITOLAK, Report::STATUS_SELESAI, Report::STATUS_DIBATALKAN], true)) {
            throw ValidationException::withMessages(['petugas_id' => 'Laporan berstatus '.$report->statusLabel().' tidak dapat ditugaskan.']);
        }
    }

    /**
     * Menolak transisi yang tidak sah, lalu menyimpan status baru.
     */
    private function applyStatus(Request $request, Report $report, string $status, ?string $catatan): void
    {
        if (! $report->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => 'Perubahan dari '.$report->statusLabel().' ke '.(Report::STATUSES[$status]['label'] ?? $status).' tidak diizinkan.',
            ]);
        }

        $from = $report->statusLabel();
        $report->changeStatus($status, $request->user(), $catatan);

        // Hanya perubahan status yang dicatat, bukan isi catatannya.
        AuditLogger::record('Mengubah status '.$from.' ke '.$report->statusLabel(), 'verifikasi', $report->kode_tiket);
    }

    /**
     * Petugas hanya boleh membuka laporan yang termasuk dalam antreannya.
     */
    private function ensureVisible(Request $request, Report $report): void
    {
        abort_unless($this->visibleReports($request)->whereKey($report->id)->exists(), 404);
    }

    /**
     * Laporan yang boleh dilihat petugas: belum ditugaskan, atau ditugaskan ke petugas ini.
     * Aturannya ada di Report::scopeVisibleToPetugas supaya fitur lain memakai aturan yang sama.
     */
    private function visibleReports(Request $request)
    {
        return Report::query()->visibleToPetugas($request->user());
    }
}
