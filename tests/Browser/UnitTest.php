<?php

namespace Tests\Browser;

use App\Models\Campus;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-01: Kelola data unit/fakultas.
 */
class UnitTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_add_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $campus = Campus::factory()->create(['nama_kampus' => 'Telkom University Bandung']);

        $this->browse(function (Browser $browser) use ($admin, $campus) {
            $browser->loginAs($admin)
                ->visitRoute('admin.units.index')
                ->clickAndWaitForReload('@add-unit')
                ->select('@campus_id', (string) $campus->id)
                ->type('@nama_unit', 'Fakultas Rekayasa Industri')
                ->clickAndWaitForReload('@save-unit')
                ->assertSee('Unit/fakultas berhasil ditambahkan.')
                ->assertSeeIn('@unit-table', 'Fakultas Rekayasa Industri')
                ->assertSeeIn('@unit-table', 'Telkom University Bandung');
        });
    }

    public function test_unit_name_must_be_unique_in_same_campus(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create(['nama_unit' => 'Fakultas Informatika']);

        $this->browse(function (Browser $browser) use ($admin, $unit) {
            $browser->loginAs($admin)
                ->visitRoute('admin.units.create')
                ->select('@campus_id', (string) $unit->campus_id)
                ->type('@nama_unit', 'Fakultas Informatika')
                ->clickAndWaitForReload('@save-unit')
                ->assertPresent('@error-nama_unit');
        });
    }

    public function test_admin_can_edit_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create(['nama_unit' => 'Unit Lama']);

        $this->browse(function (Browser $browser) use ($admin, $unit) {
            $browser->loginAs($admin)
                ->visitRoute('admin.units.index')
                ->clickAndWaitForReload('@edit-unit-'.$unit->id)
                ->clear('@nama_unit')
                ->type('@nama_unit', 'Unit Baru')
                ->clickAndWaitForReload('@save-unit')
                ->assertSee('Unit/fakultas berhasil diperbarui.')
                ->assertSeeIn('@unit-table', 'Unit Baru');
        });
    }

    public function test_admin_can_deactivate_unit(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $unit) {
            $browser->loginAs($admin)
                ->visitRoute('admin.units.index')
                ->click('@toggle-unit-'.$unit->id)
                ->waitForDialog()->acceptDialog()
                ->waitForText('Unit/fakultas dinonaktifkan.')
                ->assertSeeIn('@unit-row-'.$unit->id, 'Nonaktif');
        });
    }

    public function test_non_admin_cannot_open_unit_page(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->browse(function (Browser $browser) use ($petugas) {
            $browser->loginAs($petugas)
                ->visitRoute('admin.units.index')
                ->assertSee('403');
        });
    }
}
