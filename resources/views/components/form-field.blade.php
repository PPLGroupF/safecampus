@props(['label', 'name', 'required' => false, 'hint' => null])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $name }}" class="sc-label">
        {{ $label }} @if ($required)<span class="text-brand-600">*</span>@endif
    </label>

    {{ $slot }}

    @if ($hint)
        <p class="text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-600" dusk="error-{{ $name }}">{{ $message }}</p>
    @enderror
</div>
