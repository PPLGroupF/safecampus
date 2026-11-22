<?php

namespace Tests\Feature\Petugas;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_flow_with_history_and_audit(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create();

        $this->actingAs($petugas)->get(route('petugas.laporan.show', $report))->assertOk();
        $this->assertSame('dalam_verifikasi', $report->fresh()->status);

        $this->actingAs($petugas)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'terima'])->assertSessionHasNoErrors();
        $this->actingAs($petugas)->patch(route('petugas.laporan.status', $report), ['status' => 'selesai', 'catatan' => 'Ringkasan dummy.'])->assertSessionHasNoErrors();

        $report->refresh();
        $this->assertSame('selesai', $report->status);
        $this->assertSame(['dalam_verifikasi', 'dalam_penanganan', 'selesai'], $report->statuses->pluck('status_baru')->all());
        $this->assertNull($report->statuses->first()->diubah_oleh);
        $this->assertSame($petugas->id, $report->statuses->last()->diubah_oleh);
        $this->assertDatabaseHas('audit_logs', ['modul' => 'verifikasi', 'kode_tiket' => $report->kode_tiket, 'aksi' => 'Mengubah status Dalam Penanganan ke Selesai']);

        $raw = DB::table('report_statuses')->where('status_baru', 'selesai')->value('catatan');
        $this->assertStringNotContainsString('Ringkasan', $raw);
    }

    public function test_reason_required_for_reject_and_request_info(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->status('dalam_verifikasi')->create();

        $this->actingAs($petugas)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'tolak'])->assertSessionHasErrors('catatan');
        $this->actingAs($petugas)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'minta_info'])->assertSessionHasErrors('catatan');
        $this->assertSame('dalam_verifikasi', $report->fresh()->status);
    }

    public function test_invalid_transitions_are_rejected(): void
    {
        $petugas = User::factory()->petugas()->create();
        $diajukan = Report::factory()->create();
        $selesai = Report::factory()->status('selesai')->create();
        $verifikasi = Report::factory()->status('dalam_verifikasi')->create();

        $this->actingAs($petugas)->patch(route('petugas.laporan.status', $diajukan), ['status' => 'selesai', 'catatan' => 'x'])->assertSessionHasErrors('status');
        $this->actingAs($petugas)->patch(route('petugas.laporan.status', $selesai), ['status' => 'dalam_penanganan'])->assertSessionHasErrors('status');
        $this->actingAs($petugas)->patch(route('petugas.laporan.status', $verifikasi), ['status' => 'selesai', 'catatan' => 'x'])->assertSessionHasErrors('status');
        $this->actingAs($petugas)->patch(route('petugas.laporan.status', $verifikasi), ['status' => 'dibatalkan'])->assertSessionHasErrors('status');

        $this->assertSame('selesai', $selesai->fresh()->status);
    }

    public function test_reporter_sees_status_and_cannot_edit_after_verification(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create();

        $this->actingAs($petugas)->get(route('petugas.laporan.show', $report));
        $this->actingAs($petugas)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'tolak', 'catatan' => 'Alasan dummy penolakan.']);

        $this->actingAs($report->pelapor)->get(route('laporan.show', $report))->assertOk()
            ->assertSee('Ditolak')->assertSee('Alasan dummy penolakan.');
        $this->actingAs($report->pelapor)->patch(route('laporan.cancel', $report))->assertSessionHas('error');
    }

    public function test_non_officer_cannot_verify(): void
    {
        $report = Report::factory()->status('dalam_verifikasi')->create();

        $this->actingAs(User::factory()->admin()->create())->post(route('petugas.laporan.verify', $report), ['keputusan' => 'terima'])->assertForbidden();
        $this->actingAs($report->pelapor)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'terima'])->assertForbidden();
    }
}
