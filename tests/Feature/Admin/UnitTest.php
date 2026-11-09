<?php

namespace Tests\Feature\Admin;

use App\Models\Campus;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $campus = Campus::factory()->create();

        $this->actingAs($admin)->get(route('admin.units.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.units.create'))->assertOk();

        $this->actingAs($admin)->post(route('admin.units.store'), ['campus_id' => $campus->id, 'nama_unit' => 'Fakultas Informatika'])
            ->assertRedirect(route('admin.units.index'));
        $unit = Unit::firstWhere('nama_unit', 'Fakultas Informatika');

        $this->actingAs($admin)->get(route('admin.units.edit', $unit))->assertOk();
        $this->actingAs($admin)->put(route('admin.units.update', $unit), ['campus_id' => $campus->id, 'nama_unit' => 'Fakultas Teknik'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Fakultas Teknik', $unit->fresh()->nama_unit);

        $this->actingAs($admin)->patch(route('admin.units.toggle', $unit));
        $this->assertFalse($unit->fresh()->is_active);
    }

    public function test_same_name_allowed_in_different_campus_but_not_same_campus(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create(['nama_unit' => 'Fakultas Informatika']);
        $otherCampus = Campus::factory()->create();

        $this->actingAs($admin)->post(route('admin.units.store'), ['campus_id' => $unit->campus_id, 'nama_unit' => 'Fakultas Informatika'])
            ->assertSessionHasErrors('nama_unit');
        $this->actingAs($admin)->post(route('admin.units.store'), ['campus_id' => $otherCampus->id, 'nama_unit' => 'Fakultas Informatika'])
            ->assertSessionHasNoErrors();
    }

    public function test_inactive_campus_cannot_be_chosen_for_new_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $campus = Campus::factory()->create(['is_active' => false]);
        $active = Campus::factory()->create();
        $unit = Unit::factory()->create(['campus_id' => $active->id]);

        $this->actingAs($admin)->post(route('admin.units.store'), ['campus_id' => $campus->id, 'nama_unit' => 'Fakultas Baru'])
            ->assertSessionHasErrors('campus_id');
        $this->actingAs($admin)->put(route('admin.units.update', $unit), ['campus_id' => $campus->id, 'nama_unit' => $unit->nama_unit])
            ->assertSessionHasErrors('campus_id');
    }

    public function test_campus_with_active_unit_cannot_be_deactivated(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();

        $this->actingAs($admin)->patch(route('admin.campuses.toggle', $unit->campus))->assertSessionHas('error');
        $this->assertTrue($unit->campus->fresh()->is_active);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.units.index'))->assertForbidden();
    }
}
