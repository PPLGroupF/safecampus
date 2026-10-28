@php
    $menus = config('sidebar.'.auth()->user()->role, []);
@endphp

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 transform transition-transform duration-200 lg:static lg:translate-x-0 lg:flex lg:flex-col">
    <div class="flex h-16 items-center justify-between px-5 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <x-application-logo class="h-9 w-9" />
            <div class="leading-tight">
                <div class="text-lg font-bold text-gray-900">Safe<span class="text-brand-600">Campus</span></div>
                <div class="text-[10px] font-medium uppercase tracking-widest text-gray-400">Telkom University</div>
            </div>
        </a>
        <button type="button" class="lg:hidden text-gray-400 hover:text-gray-600" @click="sidebarOpen = false" aria-label="Tutup menu">
            <x-icon name="x" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-4" dusk="sidebar">
        @foreach ($menus as $menu)
            @continue(! Route::has($menu['route']))

            @php
                $active = request()->routeIs(...(array) $menu['active']);
            @endphp

            <a href="{{ route($menu['route']) }}"
               @class([
                   'group flex items-center gap-3 rounded-lg border-l-[3px] py-2.5 pl-[9px] pr-3 text-sm font-medium transition',
                   'border-brand-600 bg-brand-50 text-brand-700' => $active,
                   'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900' => ! $active,
               ])>
                <x-icon :name="$menu['icon']" :class="\Illuminate\Support\Arr::toCssClasses(['h-5 w-5 shrink-0', 'text-brand-600' => $active, 'text-gray-400 group-hover:text-gray-500' => ! $active])" />
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-gray-100 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" dusk="logout-button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                <x-icon name="logout" class="h-5 w-5 text-gray-400" />
                Keluar
            </button>
        </form>
    </div>
</aside>
