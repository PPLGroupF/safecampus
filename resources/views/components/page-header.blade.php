@props(['title', 'subtitle' => null, 'back' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        @if ($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali
            </a>
        @endif
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl" dusk="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
