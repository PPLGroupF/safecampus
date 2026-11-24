<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tanggapan pelapor pada laporannya (misalnya informasi tambahan yang diminta petugas).
 */
class ResponseController extends Controller
{
    /**
     * Semua tanggapan pelapor pada laporannya dalam satu halaman.
     * Di halaman detail laporan hanya tampil beberapa tanggapan terbaru.
     */
    public function index(Request $request, Report $report): View
    {
        $this->ensureOwner($request, $report);

        return view('reports.responses.index', compact('report'));
    }

    public function store(Request $request, Report $report): RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (! Response::isOpenFor($report)) {
            return $this->back($report)->with('error', 'Tanggapan hanya dapat dikirim saat laporan sedang diverifikasi atau ditangani.');
        }

        Response::create($this->validated($request) + [
            'report_id' => $report->id,
            'user_id' => $request->user()->id,
        ]);

        return $this->back($report)->with('success', 'Tanggapan terkirim ke petugas Satgas.');
    }

    public function update(Request $request, Response $response): RedirectResponse
    {
        $this->ensureAuthor($request, $response);

        $response->update($this->validated($request, $response));

        return $this->back($response->report)->with('success', 'Tanggapan diperbarui.');
    }

    public function destroy(Request $request, Response $response): RedirectResponse
    {
        $this->ensureAuthor($request, $response);

        $report = $response->report;
        $response->delete();

        return $this->back($report)->with('success', 'Tanggapan dihapus.');
    }

    private function validated(Request $request, ?Response $response = null): array
    {
        $bag = $response ? 'response'.$response->id : 'default';

        return $request->validateWithBag($bag, [
            'pesan' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'pesan.required' => 'Isi tanggapan wajib diisi.',
            'pesan.min' => 'Isi tanggapan minimal 5 karakter.',
        ]);
    }

    private function ensureOwner(Request $request, Report $report): void
    {
        abort_unless($report->user_id === $request->user()->id, 404);
    }

    /**
     * Hanya pelapor yang menulis tanggapan yang boleh mengubah atau menghapusnya.
     * Balasan petugas tidak dapat diubah lewat rute pelapor.
     */
    private function ensureAuthor(Request $request, Response $response): void
    {
        abort_unless(! $response->isReply() && $response->user_id === $request->user()->id, 404);
    }

    private function back(Report $report): RedirectResponse
    {
        return redirect()->to(route('laporan.show', $report).'#tanggapan');
    }
}
