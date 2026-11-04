<?php

namespace Tests\Feature\Admin;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_campus(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.campuses.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.campuses.create'))->assertOk();

        $this->actingAs($admin)->post(route('admin.campuses.store'), ['nama_kampus' => 'Telkom University Bandung'])
            ->assertRedirect(route('admin.campuses.index'));
        $campus = Campus::firstWhere('nama_kampus', 'Telkom University Bandung');
        $this->assertNotNull($campus);

        $this->actingAs($admin)->get(route('admin.campuses.edit', $campus))->assertOk();
        $this->actingAs($admin)->put(route('admin.campuses.update', $campus), ['nama_kampus' => 'Telkom University Surabaya'])
            ->assertRedirect(route('admin.campuses.index'));
        $this->assertSame('Telkom University Surabaya', $campus->fresh()->nama_kampus);

        $this->actingAs($admin)->patch(route('admin.campuses.toggle', $campus));
        $this->assertFalse($campus->fresh()->is_active);
        $this->assertDatabaseCount('campuses', 1);
    }

    public function test_duplicate_name_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Campus::factory()->create(['nama_kampus' => 'Telkom University Jakarta']);

        $this->actingAs($admin)->post(route('admin.campuses.store'), ['nama_kampus' => 'Telkom University Jakarta'])
            ->assertSessionHasErrors('nama_kampus');
    }

    public function test_non_admin_is_forbidden(): void
    {
        foreach (['mahasiswa', 'petugas'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('admin.campuses.index'))->assertForbidden();
        }
    }
}
