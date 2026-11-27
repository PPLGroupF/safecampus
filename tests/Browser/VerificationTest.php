<?php

namespace Tests\Browser;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-09: Verifikasi dan pembaruan status laporan.
 */
class VerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function newReport(): Report
    {
        $report = Report::factory()->create();
        $report->statuses()->create(['status_baru' => 'diajukan']);

        return $report;
    }

    public function test_opening_new_report_moves_it_to_verification(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = $this->newReport();

        $this->browse(function (Browser $browser) use ($petugas, $report) {
            $browser->loginAs($petugas)
                ->visitRoute('petugas.laporan.show', $report)
                ->assertSeeIn('@current-status', 'Dalam Verifikasi')
                ->assertSeeIn('@status-timeline', 'Dalam Verifikasi');
        });
    }

    public function test_officer_can_accept_and_close_report(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = $this->newReport();

        $this->browse(function (Browser $browser) use ($petugas, $report) {
            $browser->loginAs($petugas)
                ->visitRoute('petugas.laporan.show', $report)
                ->clickAndWaitFor('@choose-terima', '@submit-verification')
                ->clickAndWaitForReload('@submit-verification')
                ->assertSee('Keputusan verifikasi disimpan.')
                ->assertSeeIn('@current-status', 'Dalam Penanganan')
                ->clickAndWaitFor('@choose-update-status', '@submit-status')
                ->type('@status-note', 'Pendampingan selesai dan kasus ditindaklanjuti pimpinan.')
                ->clickAndWaitForReload('@submit-status')
                ->assertSeeIn('@current-status', 'Selesai')
                ->assertSee('status akhir');
        });
    }

    public function test_reject_requires_reason_and_reason_is_visible_to_reporter(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = $this->newReport();

        $this->browse(function (Browser $petugasBrowser, Browser $pelaporBrowser) use ($petugas, $report) {
            $petugasBrowser->loginAs($petugas)
                ->visitRoute('petugas.laporan.show', $report)
                ->clickAndWaitFor('@choose-tolak', '@submit-verification')
                ->clickAndWaitForReload('@submit-verification')
                ->assertPresent('@error-catatan')
                ->clickAndWaitFor('@choose-tolak', '@verification-note')
                ->type('@verification-note', 'Kejadian di luar lingkungan dan kewenangan kampus.')
                ->clickAndWaitForReload('@submit-verification')
                ->assertSeeIn('@current-status', 'Ditolak');

            $pelaporBrowser->loginAs($report->pelapor)
                ->visitRoute('laporan.show', $report)
                ->assertSeeIn('@current-status', 'Ditolak')
                ->assertSeeIn('@status-timeline', 'di luar lingkungan dan kewenangan kampus');
        });
    }

    public function test_request_information_then_continue_verification(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = $this->newReport();

        $this->browse(function (Browser $browser) use ($petugas, $report) {
            $browser->loginAs($petugas)
                ->visitRoute('petugas.laporan.show', $report)
                ->clickAndWaitFor('@choose-minta-info', '@verification-note')
                ->type('@verification-note', 'Mohon jelaskan waktu kejadian.')
                ->clickAndWaitForReload('@submit-verification')
                ->assertSeeIn('@current-status', 'Perlu Informasi')
                ->clickAndWaitFor('@choose-update-status', '@submit-status')
                ->clickAndWaitForReload('@submit-status')
                ->assertSeeIn('@current-status', 'Dalam Verifikasi');
        });
    }
}
