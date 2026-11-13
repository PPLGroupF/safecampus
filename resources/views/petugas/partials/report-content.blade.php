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
</x-card>
