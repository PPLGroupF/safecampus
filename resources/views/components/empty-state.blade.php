@props(['title' => 'Belum ada data', 'icon' => 'inbox'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }} dusk="empty-state">
    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="mt-3 text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if (trim($slot))
        <p class="mt-1 max-w-sm text-sm text-gray-500">{{ $slot }}</p>
    @endif
</div>
