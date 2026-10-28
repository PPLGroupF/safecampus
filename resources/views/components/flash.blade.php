@if (session('success') || session('error') || session('status'))
    @php
        $isError = (bool) session('error');
        $message = session('error') ?? session('success') ?? session('status');
    @endphp

    <div x-data="{ show: true }" x-show="show" x-transition
         @class([
             'mb-6 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm',
             'border-red-200 bg-red-50 text-red-800' => $isError,
             'border-green-200 bg-green-50 text-green-800' => ! $isError,
         ])
         role="alert" dusk="flash-message">
        <x-icon :name="$isError ? 'warning' : 'check'" class="mt-0.5 h-5 w-5 shrink-0" />
        <div class="flex-1">{{ $message }}</div>
        <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif
