<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_by_officer_notifies_reporter_without_sensitive_content(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create(['kronologi' => 'Data dummy rahasia sekali untuk tes notifikasi.']);

        // Dibuka petugas: Diajukan -> Dalam Verifikasi oleh sistem, tidak ada notifikasi.
        $this->actingAs($petugas)->get(route('petugas.laporan.show', $report));
        $this->assertSame(0, Notification::count());

        $this->actingAs($petugas)->post(route('petugas.laporan.verify', $report), ['keputusan' => 'terima']);

        $notification = Notification::sole();
        $this->assertSame($report->user_id, $notification->user_id);
        $this->assertStringContainsString($report->kode_tiket, $notification->pesan);
        $this->assertStringContainsString('Dalam Penanganan', $notification->pesan);
        $this->assertStringNotContainsString('rahasia', $notification->pesan);
        $this->assertSame(route('laporan.show', $report, false), $notification->url);
    }

    public function test_user_sees_only_own_notifications_and_unread_count(): void
    {
        $user = User::factory()->create();
        Notification::kirim($user, 'Milik saya', 'Pesan untuk saya');
        Notification::kirim(User::factory()->create(), 'Milik orang lain', 'Pesan rahasia');

        $this->actingAs($user)->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Milik saya')
            ->assertDontSee('Milik orang lain')
            ->assertSee('Belum dibaca (1)');
        $this->assertSame(1, $user->unreadNotificationCount());
    }

    public function test_opening_notification_marks_it_read_and_redirects(): void
    {
        $user = User::factory()->create();
        $notification = Notification::kirim($user, 'Status', 'Pesan', '/laporan');

        $this->actingAs($user)->patch(route('notifikasi.read', $notification))->assertRedirect('/laporan');
        $this->assertNotNull($notification->fresh()->dibaca_at);
        $this->assertSame(0, $user->unreadNotificationCount());
    }

    public function test_cannot_open_notification_of_another_user(): void
    {
        $notification = Notification::kirim(User::factory()->create(), 'Status', 'Pesan');

        $this->actingAs(User::factory()->create())->patch(route('notifikasi.read', $notification))->assertNotFound();
        $this->assertNull($notification->fresh()->dibaca_at);
    }

    public function test_mark_all_as_read(): void
    {
        $user = User::factory()->create();
        Notification::kirim($user, 'Satu', 'Pesan');
        Notification::kirim($user, 'Dua', 'Pesan');
        $other = Notification::kirim(User::factory()->create(), 'Tiga', 'Pesan');

        $this->actingAs($user)->post(route('notifikasi.read-all'))->assertSessionHas('success');
        $this->assertSame(0, $user->unreadNotificationCount());
        $this->assertNull($other->fresh()->dibaca_at);
    }

    public function test_guest_cannot_open_notifications(): void
    {
        $this->get(route('notifikasi.index'))->assertRedirect(route('login'));
    }
}
