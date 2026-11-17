<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-08: Antrean dan detail laporan untuk petugas.
 */
class PetugasQueueTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_queue_shows_reports_and_can_be_filtered(): void
    {
        $petugas = User::factory()->petugas()->create();
        $perundungan = Category::factory()->create(['nama_kategori' => 'Perundungan']);
        $a = Report::factory()->create(['category_id' => $perundungan->id]);
        $b = Report::factory()->status('dalam_penanganan')->create();

        $this->browse(function (Browser $browser) use ($petugas, $perundungan, $a, $b) {
            $browser->loginAs($petugas)
                ->visit('/dashboard')
                ->assertRouteIs('petugas.antrean')
                ->assertSeeIn('@queue-table', $a->kode_tiket)
                ->assertSeeIn('@queue-table', $b->kode_tiket)
                ->select('@filter-category', (string) $perundungan->id)
                ->clickAndWaitForReload('@apply-filter')
                ->assertSeeIn('@queue-table', $a->kode_tiket)
                ->assertDontSeeIn('@queue-table', $b->kode_tiket);
        });
    }

    public function test_detail_shows_decrypted_chronology_and_identity_for_identified_report(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->create(['nama' => 'Rizky Pratama']);
        $report = Report::factory()->create(['user_id' => $pelapor->id, 'kronologi' => 'Data dummy: kronologi rahasia yang hanya untuk petugas Satgas.']);

        $this->browse(function (Browser $browser) use ($petugas, $report) {
            $browser->loginAs($petugas)
                ->visitRoute('petugas.antrean')
                ->clickAndWaitForReload('@open-'.$report->id)
                ->assertSeeIn('@kronologi', 'kronologi rahasia')
                ->assertSeeIn('@reporter-name', 'Rizky Pratama');
        });
    }

    public function test_anonymous_reporter_identity_is_hidden(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->create(['nama' => 'Nama Asli Pelapor']);
        $report = Report::factory()->anonim()->create(['user_id' => $pelapor->id]);

        $this->browse(function (Browser $browser) use ($petugas, $report) {
            $browser->loginAs($petugas)
                ->visitRoute('petugas.antrean')
                ->assertSeeIn('@queue-row-'.$report->id, 'Pelapor Anonim')
                ->assertDontSee('Nama Asli Pelapor')
                ->visitRoute('petugas.laporan.show', $report)
                ->assertSeeIn('@reporter-name', 'Pelapor Anonim')
                ->assertDontSee('Nama Asli Pelapor')
                ->assertDontSee($report->pelapor->email);
        });
    }

    public function test_admin_cannot_open_report_content(): void
    {
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $report) {
            $browser->loginAs($admin)
                ->visitRoute('petugas.laporan.show', $report)
                ->assertSee('403')
                ->assertDontSee($report->kronologi);
        });
    }
}
