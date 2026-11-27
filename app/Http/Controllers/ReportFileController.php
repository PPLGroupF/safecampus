<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Bukti laporan yang disimpan di disk privat "bukti".
 * Pelapor mengunggah, mengganti, dan menghapus bukti; petugas hanya melihat.
 */
class ReportFileController extends Controller
{
    public function store(Request $request, Report $report): RedirectResponse
    {
        $this->ensureOwner($request, $report);

        if (ReportFile::where('report_id', $report->id)->count() >= ReportFile::MAX_FILES) {
            return $this->back($report)->with('error', 'Maksimal '.ReportFile::MAX_FILES.' file bukti untuk setiap laporan.');
        }

        $upload = $this->validatedFile($request);

        ReportFile::create($this->storeFile($report, $upload) + [
            'report_id' => $report->id,
            'uploaded_by' => $request->user()->id,
        ]);

        return $this->back($report)->with('success', 'Bukti berhasil diunggah.');
    }

    /**
     * Mengganti file bukti dengan file baru. File lama dihapus dari penyimpanan.
     */
    public function update(Request $request, ReportFile $file): RedirectResponse
    {
        $report = $file->report;
        $this->ensureOwner($request, $report);

        $upload = $this->validatedFile($request);
        $old = $file->path;

        $file->update($this->storeFile($report, $upload));
        Storage::disk('bukti')->delete($old);

        return $this->back($report)->with('success', 'Bukti berhasil diganti.');
    }

    public function destroy(Request $request, ReportFile $file): RedirectResponse
    {
        $report = $file->report;
        $this->ensureOwner($request, $report);

        Storage::disk('bukti')->delete($file->path);
        $file->delete();

        return $this->back($report)->with('success', 'Bukti berhasil dihapus.');
    }

    /**
     * Menampilkan (show) atau mengunduh file bukti.
     * Hanya pelapor pemilik laporan dan petugas yang berhak menangani laporan tersebut.
     * Admin tidak boleh melihat bukti (Tabel 7 dan Tabel 8 proposal).
     */
    public function show(Request $request, ReportFile $file): StreamedResponse
    {
        $user = $request->user();
        $report = $file->report;

        $allowed = $report->user_id === $user->id || $report->isVisibleToPetugas($user);
        abort_unless($allowed, 404);

        $disk = Storage::disk('bukti');
        abort_unless($disk->exists($file->path), 404);

        return $disk->response($file->path, $file->nama_file, [
            'Content-Type' => $file->mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Validasi jenis dan ukuran file: JPG, JPEG, PNG, atau PDF dengan ukuran maksimal 5 MB.
     * Jenis file diperiksa dari isinya (bukan hanya ekstensi nama file).
     */
    private function validatedFile(Request $request): UploadedFile
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:'.implode(',', ReportFile::MIMES), 'max:'.ReportFile::MAX_KB],
        ], [
            'file.required' => 'Pilih file bukti terlebih dahulu.',
            'file.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'file.uploaded' => 'File gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
        ]);

        return $request->file('file');
    }

    /**
     * Menyimpan file ke disk privat dengan nama acak, dikelompokkan per laporan.
     */
    private function storeFile(Report $report, UploadedFile $upload): array
    {
        return [
            'path' => $upload->store((string) $report->id, 'bukti'),
            'nama_file' => mb_substr($upload->getClientOriginalName(), 0, 200),
            'mime' => $upload->getMimeType(),
            'ukuran' => $upload->getSize(),
        ];
    }

    private function ensureOwner(Request $request, Report $report): void
    {
        abort_unless($report->user_id === $request->user()->id, 404);
    }

    private function back(Report $report): RedirectResponse
    {
        return redirect()->to(route('laporan.show', $report).'#bukti');
    }
}
