<x-card title="Kronologi Kejadian" subtitle="Isi laporan didekripsi hanya untuk petugas Satgas.">
    <p class="whitespace-pre-line text-sm leading-relaxed text-gray-700" dusk="kronologi">{{ $report->kronologi }}</p>
</x-card>

<x-card title="Data Terlapor">
    <dl class="grid gap-4 text-sm sm:grid-cols-2" dusk="terlapor">
        <div>
            <dt class="text-gray-500">Peran terlapor</dt>
            <dd class="font-medium text-gray-900">{{ $report->terlaporPeranLabel() }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-gray-500">Keterangan</dt>
            <dd class="whitespace-pre-line text-gray-900">{{ $report->terlapor_keterangan ?: '-' }}</dd>
        </div>
    </dl>
    @if ($report->terlapor_peran === 'petugas_satgas')
        <div class="mt-4 flex items-start gap-2 rounded-lg bg-amber-50 p-3 text-sm text-amber-800" dusk="conflict-warning">
            <x-icon name="warning" class="mt-0.5 h-4 w-4 shrink-0" />
            Terlapor adalah anggota Satgas. Pastikan laporan ditangani petugas yang tidak memiliki konflik kepentingan.
        </div>
    @endif
</x-card>
