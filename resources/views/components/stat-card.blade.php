@props(['label', 'value', 'icon' => 'document', 'color' => 'red', 'hint' => null, 'href' => null])

@php
    $tones = [
        'red' => 'bg-red-50 text-red-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'gray' => 'bg-gray-100 text-gray-600',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'sc-card flex items-center gap-4 p-5'.($href ? ' transition hover:border-brand-200 hover:shadow' : '')]) }}>
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $tones[$color] ?? $tones['gray'] }}">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <div class="min-w-0">
        <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
        <div class="text-sm font-medium text-gray-600">{{ $label }}</div>
        @if ($hint)
            <div class="mt-0.5 text-xs text-gray-400">{{ $hint }}</div>
        @endif
    </div>
</{{ $tag }}>
