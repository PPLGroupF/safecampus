<x-app-layout>
    <x-slot name="title">{{ $report->kode_tiket }}</x-slot>

    <x-page-header :title="'Detail Laporan '.$report->kode_tiket" :back="route('petugas.antrean')"
                   subtitle="Tinjau laporan dan tentukan langkah penanganan selanjutnya." />

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
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Tanggal masuk</div>
            <div class="mt-1 text-sm font-semibold text-gray-900">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</div>
        </div>
        <div class="sc-card p-4">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Status saat ini</div>
            <div class="mt-1"><x-badge :color="$report->statusColor()" dusk="current-status">{{ $report->statusLabel() }}</x-badge></div>
        </div>
    </div>

    @includeIf('petugas.partials.verification-actions')

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @includeIf('petugas.partials.report-content')
            @includeIf('petugas.partials.evidence')
            @includeIf('petugas.partials.responses')
            @includeIf('petugas.partials.schedules')
            @includeIf('petugas.partials.internal-notes')
        </div>

        <div class="space-y-6">
            @includeIf('petugas.partials.reporter')
            @includeIf('petugas.partials.assignment')

            <x-card title="Riwayat Status">
                @include('reports.partials.timeline')
            </x-card>
        </div>
    </div>
</x-app-layout>
