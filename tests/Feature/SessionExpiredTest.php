<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class SessionExpiredTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_session_redirects_to_login_instead_of_419(): void
    {
        $user = User::factory()->petugas()->create();

        // Paksa token CSRF tidak cocok seperti saat sesi sudah kedaluwarsa.
        $this->app->bind(ValidateCsrfToken::class, fn () => new class($this->app, $this->app['encrypter']) extends ValidateCsrfToken {
            public function handle($request, \Closure $next)
            {
                throw new TokenMismatchException('CSRF token mismatch.');
            }
        });

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
