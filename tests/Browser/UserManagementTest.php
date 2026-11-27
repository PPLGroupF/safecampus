<?php

namespace Tests\Browser;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-04: Kelola akun pengguna.
 */
class UserManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_add_student_account(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create(['nama_unit' => 'Fakultas Informatika']);

        $this->browse(function (Browser $browser) use ($admin, $unit) {
            $browser->loginAs($admin)
                ->visitRoute('admin.users.index')
                ->clickAndWaitForReload('@add-user')
                ->waitForAlpine()
                ->type('@nama', 'Budi Mahasiswa')
                ->type('@email', 'budi@safecampus.test')
                ->select('@role', 'mahasiswa')
                ->type('@nim', '1202249999')
                ->select('@unit_id', (string) $unit->id)
                ->type('@password', 'rahasia123')
                ->clickAndWaitForReload('@save-user')
                ->assertSee('Akun berhasil ditambahkan.')
                ->assertSeeIn('@user-table', 'Budi Mahasiswa')
                ->assertSeeIn('@user-table', '1202249999');
        });
    }

    public function test_nim_is_required_for_student(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $unit) {
            $browser->loginAs($admin)
                ->visitRoute('admin.users.create')
                ->waitForAlpine()
                ->type('@nama', 'Tanpa NIM')
                ->type('@email', 'tanpanim@safecampus.test')
                ->select('@role', 'mahasiswa')
                ->select('@unit_id', (string) $unit->id)
                ->type('@password', 'rahasia123')
                ->clickAndWaitForReload('@save-user')
                ->assertPresent('@error-nim');
        });
    }

    public function test_admin_can_edit_account(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create(['nama' => 'Nama Lama']);

        $this->browse(function (Browser $browser) use ($admin, $petugas) {
            $browser->loginAs($admin)
                ->visitRoute('admin.users.edit', $petugas)
                ->waitForAlpine()
                ->clear('@nama')
                ->type('@nama', 'Nama Baru')
                ->clickAndWaitForReload('@save-user')
                ->assertSee('Akun berhasil diperbarui.')
                ->assertSeeIn('@user-table', 'Nama Baru');
        });
    }

    public function test_deactivated_account_cannot_login(): void
    {
        $admin = User::factory()->admin()->create();
        $mahasiswa = User::factory()->create(['email' => 'nonaktif@safecampus.test']);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            $browser->loginAs($admin)
                ->visitRoute('admin.users.index')
                ->click('@toggle-user-'.$mahasiswa->id)
                ->waitForDialog()->acceptDialog()
                ->waitForText('Akun dinonaktifkan.')
                ->clickAndWaitForReload('@logout-button')
                ->waitForLocation('/login')
                ->waitFor('@login-email')
                ->type('@login-email', 'nonaktif@safecampus.test')
                ->type('@login-password', 'password')
                ->clickAndWaitForReload('@login-button')
                ->assertSee('Akun Anda sedang dinonaktifkan.');
        });
    }
}
