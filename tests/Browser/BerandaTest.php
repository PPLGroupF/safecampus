<?php

namespace Tests\Browser;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-07: Beranda dan panduan pelaporan.
 */
class BerandaTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_beranda_shows_own_summary_and_latest_reports(): void
    {
        $student = User::factory()->create();
        Report::factory()->count(2)->create(['user_id' => $student->id]);
        Report::factory()->status('dalam_penanganan')->create(['user_id' => $student->id]);
        $selesai = Report::factory()->status('selesai')->create(['user_id' => $student->id]);
        Report::factory()->count(3)->create();

        $this->browse(function (Browser $browser) use ($student, $selesai) {
            $browser->loginAs($student)
                ->visit('/dashboard')
                ->assertRouteIs('mahasiswa.beranda')
                ->assertSeeIn('@stat-total', '4')
                ->assertSeeIn('@stat-verifikasi', '2')
                ->assertSeeIn('@stat-penanganan', '1')
                ->assertSeeIn('@stat-selesai', '1')
                ->assertSeeIn('@latest-reports', $selesai->kode_tiket);
        });
    }

    public function test_shortcut_buttons_and_help_information(): void
    {
        $student = User::factory()->create();

        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visitRoute('mahasiswa.beranda')
                ->assertSeeIn('@help-card', 'Kondisi darurat')
                ->clickAndWaitForReload('@shortcut-create')
                ->assertRouteIs('laporan.create')
                ->back()
                ->clickAndWaitForReload('@shortcut-guide')
                ->assertRouteIs('panduan');
        });
    }

    public function test_guide_explains_steps_rights_and_modes(): void
    {
        $student = User::factory()->create();

        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visitRoute('panduan')
                ->assertSeeIn('@guide-steps', 'Simpan kode tiket')
                ->assertSeeIn('@guide-rights', 'kerahasiaannya')
                ->assertSeeIn('@guide-modes', 'Anonim ke Petugas');
        });
    }
}
