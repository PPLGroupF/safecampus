<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Daftar notifikasi milik pengguna yang sedang login (semua peran).
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifikasi()
            ->when($request->filter === 'belum', fn ($query) => $query->unread())
            ->paginate(15)
            ->withQueryString();

        return view('notifikasi.index', [
            'notifications' => $notifications,
            'unread' => $request->user()->notifikasi()->unread()->count(),
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca, lalu buka halaman yang dituju notifikasi tersebut.
     */
    public function read(Request $request, Notification $notification): RedirectResponse
    {
        // Pengguna hanya boleh membuka notifikasi miliknya sendiri.
        abort_unless($notification->user_id === $request->user()->id, 404);

        $notification->markAsRead();

        return $notification->url
            ? redirect()->to($notification->url)
            : redirect()->route('notifikasi.index');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->notifikasi()->unread()->update(['dibaca_at' => now()]);

        return redirect()->route('notifikasi.index')->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
