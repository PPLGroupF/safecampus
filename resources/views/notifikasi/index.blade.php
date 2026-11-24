<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    <x-page-header title="Notifikasi" subtitle="Pemberitahuan terkait laporan dan aktivitas akun Anda.">
        @if ($unread > 0)
            <x-slot name="actions">
                <form method="POST" action="{{ route('notifikasi.read-all') }}">
                    @csrf
                    <x-btn variant="secondary" icon="check" dusk="read-all">Tandai semua dibaca</x-btn>
                </form>
            </x-slot>
        @endif
    </x-page-header>

    <x-card :padding="false">
        <div class="flex gap-2 border-b border-gray-100 p-4 text-sm">
            <a href="{{ route('notifikasi.index') }}" @class(['rounded-lg px-3 py-1.5 font-medium', 'bg-brand-50 text-brand-700' => request('filter') !== 'belum', 'text-gray-600 hover:bg-gray-100' => request('filter') === 'belum'])>Semua</a>
            <a href="{{ route('notifikasi.index', ['filter' => 'belum']) }}" @class(['rounded-lg px-3 py-1.5 font-medium', 'bg-brand-50 text-brand-700' => request('filter') === 'belum', 'text-gray-600 hover:bg-gray-100' => request('filter') !== 'belum']) dusk="filter-unread">
                Belum dibaca ({{ $unread }})
            </a>
        </div>

        @if ($notifications->isEmpty())
            <x-empty-state title="Belum ada notifikasi" icon="bell">Notifikasi akan muncul saat ada perkembangan pada laporan atau akun Anda.</x-empty-state>
        @else
            <ul class="divide-y divide-gray-100" dusk="notification-list">
                @foreach ($notifications as $notification)
                    <li @class(['flex items-start gap-4 px-5 py-4', 'bg-brand-50/40' => ! $notification->isRead()]) dusk="notification-{{ $notification->id }}">
                        <div @class(['mt-1 h-2.5 w-2.5 shrink-0 rounded-full', 'bg-brand-600' => ! $notification->isRead(), 'bg-gray-200' => $notification->isRead()])></div>
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-900">{{ $notification->judul }}</div>
                            <p class="text-sm text-gray-600">{{ $notification->pesan }}</p>
                            <div class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                        <form method="POST" action="{{ route('notifikasi.read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <x-btn variant="secondary" size="sm" dusk="open-notification-{{ $notification->id }}">
                                {{ $notification->url ? 'Buka' : 'Tandai dibaca' }}
                            </x-btn>
                        </form>
                    </li>
                @endforeach
            </ul>

            <div class="border-t border-gray-100 px-4 py-3">{{ $notifications->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
