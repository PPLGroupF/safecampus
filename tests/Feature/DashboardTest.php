<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_every_role_can_open_dashboard(): void
    {
        foreach (['mahasiswa', 'petugas', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/dashboard');

            if ($response->isRedirect()) {
                $response = $this->actingAs($user)->get($response->headers->get('Location'));
            }

            $response->assertOk()->assertSee($user->nama);
        }
    }
}
