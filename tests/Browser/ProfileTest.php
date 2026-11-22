<?php

namespace Tests\Browser;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-05: Kelola profil.
 */
class ProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_student_sees_own_profile_details(): void
    {
        $unit = Unit::factory()->create(['nama_unit' => 'Fakultas Rekayasa Industri']);
        $user = User::factory()->create(['nama' => 'Rizky Pratama', 'nim' => '1202240123', 'unit_id' => $unit->id]);

        $this->browse(function (Browser $browser) use ($user, $unit) {
            $browser->loginAs($user)
                ->visitRoute('profil.edit')
                ->assertSeeIn('@profile-name', 'Rizky Pratama')
                ->assertSeeIn('@profile-details', '1202240123')
                ->assertSeeIn('@profile-details', 'Fakultas Rekayasa Industri')
                ->assertSeeIn('@profile-details', $unit->campus->nama_kampus);
        });
    }

    public function test_user_can_change_name(): void
    {
        $user = User::factory()->petugas()->create(['nama' => 'Nama Lama']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visitRoute('profil.edit')
                ->clear('@nama')
                ->type('@nama', 'Nama Baru')
                ->clickAndWaitForReload('@save-profile')
                ->assertSee('Profil berhasil diperbarui.')
                ->assertSeeIn('@profile-name', 'Nama Baru');
        });
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visitRoute('profil.edit')
                ->type('@current_password', 'salah-sekali')
                ->type('@password', 'sandibaru123')
                ->type('@password_confirmation', 'sandibaru123')
                ->clickAndWaitForReload('@save-password')
                ->assertPresent('@error-current_password');
        });
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['email' => 'ganti@safecampus.test']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('@login-email', 'ganti@safecampus.test')
                ->type('@login-password', 'password')
                ->clickAndWaitForReload('@login-button')
                ->waitFor('@current-user-name')
                ->visitRoute('profil.edit')
                ->type('@current_password', 'password')
                ->type('@password', 'sandibaru123')
                ->type('@password_confirmation', 'sandibaru123')
                ->clickAndWaitForReload('@save-password')
                ->assertSee('Kata sandi berhasil diperbarui.')
                ->clickAndWaitForReload('@logout-button')
                ->waitForLocation('/login')
                ->waitFor('@login-email')
                ->type('@login-email', 'ganti@safecampus.test')
                ->type('@login-password', 'sandibaru123')
                ->clickAndWaitForReload('@login-button')
                ->waitFor('@current-user-name');
        });
    }
}
