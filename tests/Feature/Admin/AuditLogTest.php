<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_logout_and_failed_login_are_recorded(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->post('/logout');

        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Percobaan login gagal', 'status' => 'gagal']);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Login ke sistem', 'user_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Logout dari sistem', 'user_id' => $user->id]);
    }

    public function test_anonymous_action_is_recorded_without_user_id(): void
    {
        $mahasiswa = User::factory()->create();

        AuditLogger::record('Mengajukan laporan', 'laporan', 'SC-TEST', user: $mahasiswa, anonymous: true);

        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Mengajukan laporan', 'user_id' => null, 'peran' => 'mahasiswa', 'kode_tiket' => 'SC-TEST']);
    }

    public function test_log_cannot_be_updated_or_deleted(): void
    {
        $log = AuditLogger::record('Login ke sistem', 'autentikasi', user: User::factory()->create());

        $this->expectException(LogicException::class);
        $log->update(['aksi' => 'diubah']);
    }

    public function test_log_cannot_be_deleted(): void
    {
        $log = AuditLogger::record('Login ke sistem', 'autentikasi', user: User::factory()->create());

        $this->expectException(LogicException::class);
        $log->delete();
    }

    public function test_admin_can_view_and_filter_but_others_cannot(): void
    {
        $admin = User::factory()->admin()->create();
        AuditLogger::record('Menambah kampus A', 'kampus', user: $admin);
        AuditLogger::record('Menambah unit B', 'unit', user: $admin);

        $this->actingAs($admin)->get(route('admin.audit-logs.index', ['modul' => 'kampus']))
            ->assertOk()->assertSee('Menambah kampus A')->assertDontSee('Menambah unit B');

        $this->actingAs(User::factory()->petugas()->create())->get(route('admin.audit-logs.index'))->assertForbidden();
    }

    public function test_master_data_changes_are_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post(route('admin.categories.store'), ['nama_kategori' => 'Perundungan']);

        $this->assertDatabaseHas('audit_logs', ['aksi' => 'Menambah kategori Perundungan', 'modul' => 'kategori']);
        $this->assertSame(0, AuditLog::where('modul', 'kategori')->where('status', 'gagal')->count());
    }
}
