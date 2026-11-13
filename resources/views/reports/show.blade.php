<x-app-layout>
    <x-slot name="title">{{ $report->kode_tiket }}</x-slot>

    <x-page-header :title="'Laporan '.$report->kode_tiket" :back="route('laporan.index')"
                   subtitle="Pantau perkembangan laporan Anda melalui timeline status.">
        @if (view()->exists('reports.partials.owner-actions'))
            <x-slot name="actions">
                @include('reports.partials.owner-actions')
            </x-slot>
        @endif
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" dusk="report-summary">
        <div class="sc-card p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Kode tiket</div>
            <div class="mt-1 font-mono text-sm font-semibold text-gray-900" dusk="ticket-code">{{ $report->kode_tiket }}</div>
        </div>
        <div class="sc-card p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Kategori</div>
            <div class="mt-1 text-sm font-semibold text-gray-900">{{ $report->category->nama_kategori }}</div>
        </div>
        <div class="sc-card p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Tanggal lapor</div>
            <div class="mt-1 text-sm font-semibold text-gray-900">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</div>
        </div>
        <div class="sc-card p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Status saat ini</div>
            <div class="mt-1"><x-badge :color="$report->statusColor()" dusk="current-status">{{ $report->statusLabel() }}</x-badge></div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card title="Kronologi">
                <p class="whitespace-pre-line text-sm leading-relaxed text-gray-700" dusk="kronologi">{{ $report->kronologi }}</p>
            </x-card>

            @includeIf('reports.partials.show-evidence')
            @includeIf('reports.partials.show-schedules')
            @includeIf('reports.partials.show-responses')
        </div>

        <div class="space-y-6">
            <x-card title="Riwayat Status">
                @include('reports.partials.timeline')
            </x-card>

            <x-card title="Detail Laporan">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Mode identitas</dt>
                        <dd class="font-medium text-gray-900">{{ $report->modeLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Peran terlapor</dt>
                        <dd class="font-medium text-gray-900">{{ $report->terlaporPeranLabel() }}</dd>
                    </div>
                    @if ($report->terlapor_keterangan)
                        <div>
                            <dt class="text-gray-500">Keterangan terlapor</dt>
                            <dd class="whitespace-pre-line text-gray-900">{{ $report->terlapor_keterangan }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Unit asal</dt>
                        <dd class="font-medium text-gray-900">{{ $report->unit?->nama_unit ?? '-' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</x-app-layout>
