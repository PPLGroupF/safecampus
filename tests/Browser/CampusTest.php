<?php

namespace Tests\Browser;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-03: Kelola data kampus.
 */
class CampusTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_add_campus(): void
    {
        $admin = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.campuses.index')
                ->clickAndWaitForReload('@add-campus')
                ->type('@nama_kampus', 'Telkom University Bandung')
                ->clickAndWaitForReload('@save-campus')
                ->assertRouteIs('admin.campuses.index')
                ->assertSee('Kampus berhasil ditambahkan.')
                ->assertSeeIn('@campus-table', 'Telkom University Bandung');
        });
    }

    public function test_campus_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Campus::factory()->create(['nama_kampus' => 'Telkom University Jakarta']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.campuses.create')
                ->type('@nama_kampus', 'Telkom University Jakarta')
                ->clickAndWaitForReload('@save-campus')
                ->assertRouteIs('admin.campuses.create')
                ->assertPresent('@error-nama_kampus');
        });
    }

    public function test_admin_can_edit_campus(): void
    {
        $admin = User::factory()->admin()->create();
        $campus = Campus::factory()->create(['nama_kampus' => 'Kampus Lama']);

        $this->browse(function (Browser $browser) use ($admin, $campus) {
            $browser->loginAs($admin)
                ->visitRoute('admin.campuses.index')
                ->clickAndWaitForReload('@edit-campus-'.$campus->id)
                ->clear('@nama_kampus')
                ->type('@nama_kampus', 'Kampus Baru')
                ->clickAndWaitForReload('@save-campus')
                ->assertSee('Kampus berhasil diperbarui.')
                ->assertSeeIn('@campus-table', 'Kampus Baru');
        });
    }

    public function test_admin_can_deactivate_campus(): void
    {
        $admin = User::factory()->admin()->create();
        $campus = Campus::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $campus) {
            $browser->loginAs($admin)
                ->visitRoute('admin.campuses.index')
                ->click('@toggle-campus-'.$campus->id)
                ->waitForDialog()->acceptDialog()
                ->waitForText('Kampus dinonaktifkan.')
                ->assertSeeIn('@campus-row-'.$campus->id, 'Nonaktif');
        });

        $this->assertFalse($campus->fresh()->is_active);
    }

    public function test_non_admin_cannot_open_campus_page(): void
    {
        $mahasiswa = User::factory()->create();

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visitRoute('admin.campuses.index')
                ->assertSee('403');
        });
    }
}
