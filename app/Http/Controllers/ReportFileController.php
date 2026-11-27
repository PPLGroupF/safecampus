<?php

namespace App\Http\Controllers;

use App\Models\ReportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Bukti laporan yang disimpan di disk privat "bukti".
 */
class ReportFileController extends Controller
{
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
}
