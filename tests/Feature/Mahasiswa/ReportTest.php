<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Category;
use App\Models\Report;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => Category::factory()->create()->id,
            'kronologi' => 'Data dummy: kejadian terjadi di kantin kampus pada siang hari dan disaksikan teman.',
            'terlapor_peran' => 'mahasiswa',
            'terlapor_keterangan' => 'Kakak tingkat',
            'mode_identitas' => 'teridentifikasi',
        ], $overrides);
    }

    public function test_student_can_create_report_with_ticket_unit_and_encryption(): void
    {
        $student = User::factory()->create(['unit_id' => Unit::factory()->create()->id]);

        $this->actingAs($student)->get(route('laporan.create'))->assertOk();
        $response = $this->actingAs($student)->post(route('laporan.store'), $this->payload(['unit_id' => 999]));

        $report = Report::first();
        $response->assertRedirect(route('laporan.show', $report));
        $this->assertSame('diajukan', $report->status);
        $this->assertSame($student->unit_id, $report->unit_id);
        $this->assertMatchesRegularExpression('/^SC-\d{8}-[A-Z0-9]{5}$/', $report->kode_tiket);
        $this->assertCount(1, $report->statuses);

        $raw = DB::table('reports')->first();
        $this->assertStringNotContainsString('kantin', $raw->kronologi);
        $this->assertStringNotContainsString('Kakak', $raw->terlapor_keterangan);
        $this->assertStringContainsString('kantin', $report->kronologi);
    }

    public function test_anonymous_submission_is_logged_without_user_id(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('laporan.store'), $this->payload(['mode_identitas' => 'anonim']));

        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Mengajukan laporan', 'user_id' => null, 'peran' => 'mahasiswa']);
    }

    public function test_validation_and_inactive_category(): void
    {
        $student = User::factory()->create();
        $inactive = Category::factory()->create(['is_active' => false]);

        $this->actingAs($student)->post(route('laporan.store'), $this->payload([
            'category_id' => $inactive->id, 'kronologi' => 'pendek', 'mode_identitas' => 'x',
        ]))->assertSessionHasErrors(['category_id', 'kronologi', 'mode_identitas']);
    }

    public function test_list_only_shows_own_reports_and_detail_is_owner_only(): void
    {
        $student = User::factory()->create();
        $mine = Report::factory()->create(['user_id' => $student->id]);
        $other = Report::factory()->create();

        $this->actingAs($student)->get(route('laporan.index'))->assertOk()
            ->assertSee($mine->kode_tiket)->assertDontSee($other->kode_tiket);
        $this->actingAs($student)->get(route('laporan.show', $mine))->assertOk();
        $this->actingAs($student)->get(route('laporan.show', $other))->assertNotFound();
        $this->actingAs($student)->get(route('laporan.edit', $other))->assertNotFound();
    }

    public function test_edit_and_cancel_only_before_verification(): void
    {
        $student = User::factory()->create();
        $report = Report::factory()->create(['user_id' => $student->id]);

        $this->actingAs($student)->get(route('laporan.edit', $report))->assertOk();
        $this->actingAs($student)->put(route('laporan.update', $report), $this->payload(['category_id' => $report->category_id]))
            ->assertSessionHasNoErrors();

        $this->actingAs($student)->patch(route('laporan.cancel', $report));
        $this->assertSame('dibatalkan', $report->fresh()->status);
        $this->assertDatabaseHas('reports', ['id' => $report->id]);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Membatalkan laporan', 'kode_tiket' => $report->kode_tiket]);

        $verified = Report::factory()->status('dalam_verifikasi')->create(['user_id' => $student->id]);
        $this->actingAs($student)->get(route('laporan.edit', $verified))->assertRedirect(route('laporan.show', $verified));
        $this->actingAs($student)->patch(route('laporan.cancel', $verified))->assertSessionHas('error');
        $this->assertSame('dalam_verifikasi', $verified->fresh()->status);
    }

    public function test_only_students_can_access_report_pages(): void
    {
        $this->actingAs(User::factory()->petugas()->create())->get(route('laporan.create'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get(route('laporan.index'))->assertForbidden();
    }
}
