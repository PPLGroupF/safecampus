<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_counts_only_own_reports(): void
    {
        $student = User::factory()->create();
        Report::factory()->create(['user_id' => $student->id]);
        Report::factory()->status('perlu_informasi')->create(['user_id' => $student->id]);
        Report::factory()->status('selesai')->create(['user_id' => $student->id]);
        Report::factory()->count(5)->create();

        $response = $this->actingAs($student)->get(route('mahasiswa.beranda'))->assertOk();

        $this->assertSame(['total' => 3, 'verifikasi' => 2, 'penanganan' => 0, 'selesai' => 1], $response->viewData('summary'));
        $this->assertCount(3, $response->viewData('latest'));
    }

    public function test_guide_is_available_and_role_restricted(): void
    {
        $this->actingAs(User::factory()->create())->get(route('panduan'))->assertOk()->assertSee('Langkah melapor');
        $this->actingAs(User::factory()->petugas()->create())->get(route('mahasiswa.beranda'))->assertForbidden();
    }
}
