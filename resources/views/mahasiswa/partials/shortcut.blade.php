<div class="mt-6 overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50 to-white p-6 sm:flex sm:items-center sm:justify-between" dusk="shortcut-card">
    <div class="flex items-center gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-sm">
            <x-icon name="document-plus" class="h-7 w-7" />
        </div>
        <div>
            <div class="text-lg font-bold text-gray-900">Buat Laporan Baru</div>
            <p class="text-sm text-gray-600">Mengalami atau menyaksikan kekerasan? Sampaikan melalui SafeCampus untuk kampus yang lebih aman.</p>
        </div>
    </div>
    <div class="mt-4 flex gap-2 sm:mt-0">
        <x-btn variant="secondary" :href="route('panduan')" icon="book" dusk="shortcut-guide">Baca Panduan</x-btn>
        <x-btn :href="route('laporan.create')" icon="plus" dusk="shortcut-create">Buat Laporan</x-btn>
    </div>
</div>
