<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Pengujian dasar login dan logout (bukan PBI, prasyarat semua fitur).
 */
class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'mahasiswa@safecampus.test']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('@login-email', 'mahasiswa@safecampus.test')
                ->type('@login-password', 'password')
                ->clickAndWaitForReload('@login-button')
                ->waitFor('@current-user-name')
                ->assertSeeIn('@current-user-name', $user->nama)
                ->clickAndWaitForReload('@logout-button')
                ->waitForLocation('/login')
                ->waitFor('@login-email')
                ->assertSee('Masuk');
        });
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['email' => 'mahasiswa@safecampus.test']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('@login-email', 'mahasiswa@safecampus.test')
                ->type('@login-password', 'salah')
                ->clickAndWaitForReload('@login-button')
                ->assertSee('Email atau kata sandi salah.');
        });
    }
}
