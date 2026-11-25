@php
    $user = auth()->user();
    $hasNotifications = Route::has('notifikasi.index') && method_exists($user, 'unreadNotificationCount');
    $unread = $hasNotifications ? $user->unreadNotificationCount() : 0;
    $initials = collect(explode(' ', $user->nama))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp

<header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-gray-100 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    <button type="button" class="lg:hidden text-gray-500 hover:text-gray-700" @click="sidebarOpen = true" aria-label="Buka menu">
        <x-icon name="menu" class="h-6 w-6" />
    </button>

    <div class="flex-1"></div>

    @if ($hasNotifications)
        @php($latest = $user->notifikasi()->take(5)->get())
        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
            <button type="button" @click="open = ! open" class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700" dusk="notification-bell" aria-label="Notifikasi">
                <x-icon name="bell" class="h-6 w-6" />
                @if ($unread > 0)
                    <span class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold text-white" dusk="notification-count">
                        {{ $unread > 99 ? '99+' : $unread }}
                    </span>
                @endif
            </button>

            <div x-show="open" x-cloak x-transition @click.outside="open = false"
                 class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg" dusk="notification-dropdown">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                    <span class="text-sm font-semibold text-gray-900">Notifikasi</span>
                    <span class="text-xs text-gray-500">{{ $unread }} belum dibaca</span>
                </div>
                @forelse ($latest as $item)
                    <form method="POST" action="{{ route('notifikasi.read', $item) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" @class(['block w-full px-4 py-3 text-left hover:bg-gray-50', 'bg-brand-50/40' => ! $item->isRead()]) dusk="dropdown-notification-{{ $item->id }}">
                            <span class="block text-sm font-medium text-gray-900">{{ $item->judul }}</span>
                            <span class="block truncate text-xs text-gray-500">{{ $item->pesan }}</span>
                        </button>
                    </form>
                @empty
                    <p class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</p>
                @endforelse
                <a href="{{ route('notifikasi.index') }}" class="block border-t border-gray-100 px-4 py-2.5 text-center text-sm font-semibold text-brand-700 hover:bg-gray-50" dusk="notification-see-all">Lihat semua notifikasi</a>
            </div>
        </div>
    @endif

    <div class="flex items-center gap-3 border-l border-gray-100 pl-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
            {{ strtoupper($initials) }}
        </div>
        <div class="hidden leading-tight sm:block">
            <div class="text-sm font-semibold text-gray-900" dusk="current-user-name">{{ $user->nama }}</div>
            <div class="text-xs text-gray-500">{{ $user->roleLabel() }}</div>
        </div>
    </div>
</header>
