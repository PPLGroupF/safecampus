<?php

namespace Tests\Feature\Petugas;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_lists_active_reports_and_hides_cancelled(): void
    {
        $petugas = User::factory()->petugas()->create();
        $aktif = Report::factory()->create();
        $selesai = Report::factory()->status('selesai')->create();
        $batal = Report::factory()->status('dibatalkan')->create();

        $this->actingAs($petugas)->get(route('petugas.antrean'))->assertOk()
            ->assertSee($aktif->kode_tiket)->assertDontSee($selesai->kode_tiket)->assertDontSee($batal->kode_tiket);
        $this->actingAs($petugas)->get(route('petugas.antrean', ['status' => 'selesai']))->assertSee($selesai->kode_tiket);
        $this->actingAs($petugas)->get(route('petugas.laporan.show', $batal))->assertNotFound();
    }

    public function test_identity_masking_rules(): void
    {
        $petugas = User::factory()->petugas()->create();
        $admin = User::factory()->admin()->create();
        $pelapor = User::factory()->create();
        $anonim = Report::factory()->anonim()->create(['user_id' => $pelapor->id]);
        $terbuka = Report::factory()->create(['user_id' => $pelapor->id]);

        $this->assertTrue($anonim->pelaporUntuk($petugas)['anonim']);
        $this->assertSame('Pelapor Anonim', $anonim->pelaporUntuk($petugas)['nama']);
        $this->assertFalse($anonim->pelaporUntuk($pelapor)['anonim']);
        $this->assertFalse($terbuka->pelaporUntuk($petugas)['anonim']);
        $this->assertSame($pelapor->nama, $terbuka->pelaporUntuk($petugas)['nama']);
        $this->assertTrue($terbuka->pelaporUntuk($admin)['anonim']);

        $this->actingAs($petugas)->get(route('petugas.laporan.show', $anonim))->assertOk()
            ->assertDontSee($pelapor->nama)->assertDontSee($pelapor->email)->assertSee($anonim->kronologi);
    }

    public function test_only_officers_can_access(): void
    {
        $report = Report::factory()->create();

        $this->actingAs(User::factory()->admin()->create())->get(route('petugas.antrean'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get(route('petugas.laporan.show', $report))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('petugas.laporan.show', $report))->assertForbidden();
    }
}
