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
        <a href="{{ route('notifikasi.index') }}" class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700" dusk="notification-bell" aria-label="Notifikasi">
            <x-icon name="bell" class="h-6 w-6" />
            @if ($unread > 0)
                <span class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold text-white" dusk="notification-count">
                    {{ $unread > 99 ? '99+' : $unread }}
                </span>
            @endif
        </a>
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
