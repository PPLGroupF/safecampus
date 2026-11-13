<?php

namespace Tests\Browser;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-10: Audit log.
 */
class AuditLogTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_login_and_master_data_changes_are_recorded(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@safecampus.test']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('@login-email', 'admin@safecampus.test')
                ->type('@login-password', 'password')
                ->clickAndWaitForReload('@login-button')
                ->waitFor('@current-user-name')
                ->visitRoute('admin.campuses.create')
                ->type('@nama_kampus', 'Telkom University Bandung')
                ->clickAndWaitForReload('@save-campus')
                ->visitRoute('admin.audit-logs.index')
                ->assertSeeIn('@audit-table', 'Login ke sistem')
                ->assertSeeIn('@audit-table', 'Menambah kampus Telkom University Bandung');
        });
    }

    public function test_admin_can_filter_logs_by_module(): void
    {
        $admin = User::factory()->admin()->create();
        AuditLog::create(['user_id' => $admin->id, 'peran' => 'admin', 'aksi' => 'Mengubah kategori Perundungan', 'modul' => 'kategori']);
        AuditLog::create(['user_id' => $admin->id, 'peran' => 'admin', 'aksi' => 'Menambah unit Fakultas Informatika', 'modul' => 'unit']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.audit-logs.index')
                ->select('@filter-modul', 'kategori')
                ->clickAndWaitForReload('@apply-filter')
                ->assertSeeIn('@audit-table', 'Mengubah kategori Perundungan')
                ->assertDontSeeIn('@audit-table', 'Menambah unit Fakultas Informatika');
        });
    }

    public function test_audit_log_page_has_no_edit_or_delete_action(): void
    {
        $admin = User::factory()->admin()->create();
        AuditLog::create(['user_id' => $admin->id, 'peran' => 'admin', 'aksi' => 'Login ke sistem', 'modul' => 'autentikasi']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.audit-logs.index')
                ->assertDontSee('Hapus')
                ->assertDontSee('Ubah');
        });
    }

    public function test_non_admin_cannot_open_audit_log(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->browse(function (Browser $browser) use ($petugas) {
            $browser->loginAs($petugas)
                ->visitRoute('admin.audit-logs.index')
                ->assertSee('403');
        });
    }
}
