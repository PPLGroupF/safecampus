<?php

namespace Tests\Feature\Admin;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student_and_officer(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.create'))->assertOk();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'Budi', 'email' => 'budi@x.test', 'role' => 'mahasiswa', 'nim' => '123456', 'unit_id' => $unit->id, 'password' => 'rahasia123',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'Siti', 'email' => 'siti@x.test', 'role' => 'petugas', 'nim' => '999', 'password' => 'rahasia123',
        ])->assertSessionHasNoErrors();

        $budi = User::firstWhere('email', 'budi@x.test');
        $this->assertTrue(Hash::check('rahasia123', $budi->password));
        $this->assertNotSame('rahasia123', $budi->password);
        $this->assertNull(User::firstWhere('email', 'siti@x.test')->nim);
        $this->assertDatabaseHas('audit_logs', ['modul' => 'pengguna', 'aksi' => 'Menambah akun budi@x.test (Mahasiswa)']);
    }

    public function test_nim_and_email_must_be_unique_and_nim_required_for_student(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();
        User::factory()->create(['email' => 'ada@x.test', 'nim' => '111']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'A', 'email' => 'ada@x.test', 'role' => 'mahasiswa', 'nim' => '111', 'unit_id' => $unit->id, 'password' => 'rahasia123',
        ])->assertSessionHasErrors(['email', 'nim']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'B', 'email' => 'b@x.test', 'role' => 'mahasiswa', 'password' => 'rahasia123',
        ])->assertSessionHasErrors(['nim', 'unit_id']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'C', 'email' => 'c@x.test', 'role' => 'mahasiswa', 'nim' => '12A45', 'unit_id' => $unit->id, 'password' => 'rahasia123',
        ])->assertSessionHasErrors(['nim' => 'NIM hanya boleh berisi angka.']);
    }

    public function test_admin_can_update_and_deactivate_but_not_self(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $petugas), [
            'nama' => 'Petugas Baru', 'email' => $petugas->email, 'role' => 'petugas',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Petugas Baru', $petugas->fresh()->nama);

        $this->actingAs($admin)->patch(route('admin.users.toggle', $petugas));
        $this->assertFalse($petugas->fresh()->is_active);

        $this->actingAs($admin)->patch(route('admin.users.toggle', $admin))->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->is_active);

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'nama' => $admin->nama, 'email' => $admin->email, 'role' => 'petugas',
        ])->assertSessionHas('error');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_inactive_user_cannot_login_and_is_logged_out(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Akun Anda sedang dinonaktifkan. Hubungi admin SafeCampus.']);
        $this->assertGuest();

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unit_with_active_user_cannot_be_deactivated(): void
    {
        $admin = User::factory()->admin()->create();
        $unit = Unit::factory()->create();
        User::factory()->create(['unit_id' => $unit->id]);

        $this->actingAs($admin)->patch(route('admin.units.toggle', $unit))->assertSessionHas('error');
        $this->assertTrue($unit->fresh()->is_active);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->petugas()->create())->get(route('admin.users.index'))->assertForbidden();
    }
}
