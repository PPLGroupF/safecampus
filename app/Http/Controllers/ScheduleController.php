<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Schedule;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Jadwal pendampingan.
 * Petugas: membuat, melihat, mengubah, dan membatalkan jadwal.
 * Pelapor: mengonfirmasi kehadiran.
 */
class ScheduleController extends Controller
{
    public function store(Request $request, Report $report): RedirectResponse
    {
        $this->ensurePetugasCanAccess($request, $report);

        if ($report->status !== Report::STATUS_DALAM_PENANGANAN) {
            throw ValidationException::withMessages(['waktu' => 'Jadwal hanya dapat dibuat untuk laporan berstatus Dalam Penanganan.']);
        }

        $schedule = Schedule::create($this->validated($request) + [
            'report_id' => $report->id,
            'petugas_id' => $request->user()->id,
            'status' => Schedule::STATUS_MENUNGGU,
        ]);

        AuditLogger::record('Membuat jadwal '.$schedule->jenisLabel(), 'pendampingan', $report->kode_tiket);

        return redirect()->to(route('petugas.laporan.show', $report).'#jadwal')->with('success', 'Jadwal pendampingan dibuat. Pelapor dapat mengonfirmasi kehadirannya.');
    }

    public function show(Request $request, Schedule $schedule): View
    {
        $this->ensurePetugasCanAccess($request, $schedule->report);

        $schedule->load(['report', 'petugas']);

        return view('petugas.schedules.show', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $this->ensurePetugasCanAccess($request, $schedule->report);

        if ($schedule->isCancelled()) {
            return back()->with('error', 'Jadwal yang sudah dibatalkan tidak dapat diubah.');
        }

        $schedule->fill($this->validated($request));

        // Perubahan waktu atau lokasi membutuhkan konfirmasi ulang dari pelapor.
        if ($schedule->isDirty(['waktu', 'lokasi'])) {
            $schedule->status = Schedule::STATUS_MENUNGGU;
            $schedule->dikonfirmasi_at = null;
        }

        $schedule->save();
        AuditLogger::record('Mengubah jadwal '.$schedule->jenisLabel(), 'pendampingan', $schedule->report->kode_tiket);

        return redirect()->route('petugas.jadwal.show', $schedule)->with('success', 'Jadwal pendampingan diperbarui.');
    }

    /**
     * Jadwal tidak dihapus permanen, melainkan dibatalkan, agar pelapor tetap melihat riwayatnya.
     */
    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        $this->ensurePetugasCanAccess($request, $schedule->report);

        if (! $schedule->isCancelled()) {
            $schedule->update(['status' => Schedule::STATUS_DIBATALKAN]);
            AuditLogger::record('Membatalkan jadwal '.$schedule->jenisLabel(), 'pendampingan', $schedule->report->kode_tiket);
        }

        return redirect()->to(route('petugas.laporan.show', $schedule->report).'#jadwal')->with('success', 'Jadwal pendampingan dibatalkan.');
    }

    /**
     * Pelapor mengonfirmasi akan hadir pada jadwal.
     */
    public function konfirmasi(Request $request, Schedule $schedule): RedirectResponse
    {
        $report = $schedule->report;
        abort_unless($report->user_id === $request->user()->id, 404);

        if (! $schedule->canBeConfirmed()) {
            return redirect()->route('laporan.show', $report)->with('error', 'Jadwal ini tidak dapat dikonfirmasi.');
        }

        $schedule->update(['status' => Schedule::STATUS_DIKONFIRMASI, 'dikonfirmasi_at' => now()]);
        AuditLogger::record('Mengonfirmasi jadwal pendampingan', 'pendampingan', $report->kode_tiket, anonymous: true);

        return redirect()->route('laporan.show', $report)->with('success', 'Terima kasih, kehadiran Anda pada jadwal pendampingan sudah dikonfirmasi.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'jenis' => ['required', Rule::in(array_keys(Schedule::JENIS))],
            'waktu' => ['required', 'date', 'after:now'],
            'lokasi' => ['required', 'string', 'max:150'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'waktu.required' => 'Tanggal dan jam wajib diisi.',
            'waktu.after' => 'Tanggal dan jam harus setelah waktu sekarang.',
            'lokasi.required' => 'Lokasi wajib diisi.',
        ]);
    }

    private function ensurePetugasCanAccess(Request $request, Report $report): void
    {
        abort_unless($report->isVisibleToPetugas($request->user()), 404);
    }
}
