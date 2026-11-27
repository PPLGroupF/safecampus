<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Report;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-06: Kelola laporan (mahasiswa).
 */
class ReportTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function student(): User
    {
        return User::factory()->create(['unit_id' => Unit::factory()->create()->id]);
    }

    private function fillForm(Browser $browser, Category $category, string $mode): Browser
    {
        return $browser->waitForAlpine()
            ->select('@category_id', (string) $category->id)
            ->type('@kronologi', 'Data dummy: pada hari Senin pukul 10.00 di ruang kelas saya mengalami ejekan berulang.')
            ->click('@next-step')
            ->click('@next-step')
            ->select('@terlapor_peran', 'mahasiswa')
            ->type('@terlapor_keterangan', 'Teman satu kelas')
            ->click('@next-step')
            ->click('@mode-'.$mode)
            ->clickAndWaitForReload('@submit-report');
    }

    public function test_student_can_submit_identified_report(): void
    {
        $student = $this->student();
        $category = Category::factory()->create(['nama_kategori' => 'Perundungan']);

        $this->browse(function (Browser $browser) use ($student, $category) {
            $browser->loginAs($student)->visitRoute('laporan.create');
            $this->fillForm($browser, $category, 'teridentifikasi')
                ->assertSee('Laporan berhasil dikirim.')
                ->assertSeeIn('@current-status', 'Diajukan')
                ->assertSeeIn('@status-timeline', 'Diajukan');
        });

        $report = Report::first();
        $this->assertSame('teridentifikasi', $report->mode_identitas);
        $this->assertSame($student->unit_id, $report->unit_id);
        $this->assertStringStartsWith('SC-', $report->kode_tiket);
    }

    public function test_student_can_submit_anonymous_report(): void
    {
        $student = $this->student();
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($student, $category) {
            $browser->loginAs($student)->visitRoute('laporan.create');
            $this->fillForm($browser, $category, 'anonim')
                ->assertSee('Laporan berhasil dikirim.');
        });

        $this->assertSame('anonim', Report::first()->mode_identitas);
    }

    public function test_short_chronology_is_rejected(): void
    {
        $student = $this->student();
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($student, $category) {
            $browser->loginAs($student)
                ->visitRoute('laporan.create')
                ->waitForAlpine()
                ->select('@category_id', (string) $category->id)
                ->script("document.getElementById('kronologi').removeAttribute('minlength');");
            $browser->type('@kronologi', 'Terlalu pendek')
                ->click('@step-4')
                ->clickAndWaitForReload('@submit-report')
                ->assertPresent('@error-kronologi');
        });
    }

    public function test_student_sees_list_and_detail(): void
    {
        $student = $this->student();
        $report = Report::factory()->create(['user_id' => $student->id]);
        $report->statuses()->create(['status_baru' => 'diajukan']);

        $this->browse(function (Browser $browser) use ($student, $report) {
            $browser->loginAs($student)
                ->visitRoute('laporan.index')
                ->assertSeeIn('@report-table', $report->kode_tiket)
                ->clickAndWaitForReload('@detail-'.$report->id)
                ->assertSeeIn('@ticket-code', $report->kode_tiket)
                ->assertSeeIn('@kronologi', $report->kronologi);
        });
    }

    public function test_student_can_edit_and_cancel_before_verification(): void
    {
        $student = $this->student();
        $report = Report::factory()->create(['user_id' => $student->id]);
        $report->statuses()->create(['status_baru' => 'diajukan']);

        $this->browse(function (Browser $browser) use ($student, $report) {
            $browser->loginAs($student)
                ->visitRoute('laporan.show', $report)
                ->clickAndWaitForReload('@edit-report')
                ->waitForAlpine()
                ->clear('@kronologi')
                ->type('@kronologi', 'Data dummy: kronologi yang sudah diperbaiki oleh pelapor dengan lebih lengkap.')
                ->click('@step-4')
                ->clickAndWaitForReload('@submit-report')
                ->assertSee('Laporan berhasil diperbarui.')
                ->assertSeeIn('@kronologi', 'sudah diperbaiki')
                ->click('@cancel-report')
                ->waitForDialog()->acceptDialog()
                ->waitForText('Laporan telah dibatalkan.')
                ->assertSeeIn('@current-status', 'Dibatalkan')
                ->assertMissing('@edit-report');
        });

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'dibatalkan']);
    }

    public function test_student_cannot_open_other_students_report(): void
    {
        $student = $this->student();
        $other = Report::factory()->create();

        $this->browse(function (Browser $browser) use ($student, $other) {
            $browser->loginAs($student)
                ->visitRoute('laporan.show', $other)
                ->assertSee('404');
        });
    }
}
