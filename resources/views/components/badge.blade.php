@props(['color' => 'gray'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'red' => 'bg-red-50 text-red-700 ring-red-200',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'green' => 'bg-green-50 text-green-700 ring-green-200',
        'purple' => 'bg-purple-50 text-purple-700 ring-purple-200',
        'slate' => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset '.($colors[$color] ?? $colors['gray'])]) }}>
    {{ $slot }}
</span>
