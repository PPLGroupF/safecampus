<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_can_view_profile(): void
    {
        foreach (['mahasiswa', 'petugas', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('profil.edit'))->assertOk()->assertSee($user->email);
        }
    }

    public function test_only_name_can_be_changed(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'nim' => '111']);

        $this->actingAs($user)->put(route('profil.update'), [
            'nama' => 'Nama Baru', 'role' => 'admin', 'email' => 'lain@x.test', 'nim' => '999',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Nama Baru', $user->nama);
        $this->assertSame('mahasiswa', $user->role);
        $this->assertSame('111', $user->nim);
        $this->assertNotSame('lain@x.test', $user->email);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profil.password'), [
            'current_password' => 'salah', 'password' => 'sandibaru123', 'password_confirmation' => 'sandibaru123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('profil.password'), [
            'current_password' => 'password', 'password' => 'sandibaru123', 'password_confirmation' => 'sandibaru123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('sandibaru123', $user->fresh()->password));
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Mengganti kata sandi', 'user_id' => $user->id]);
    }

    public function test_guest_cannot_open_profile(): void
    {
        $this->get(route('profil.edit'))->assertRedirect('/login');
    }
}
