<x-app-layout>
    <x-slot name="title">Tanggapan</x-slot>

    <x-page-header title="Semua Tanggapan" :subtitle="'Laporan '.$report->kode_tiket" :back="route('laporan.show', $report).'#tanggapan'" />

    <div class="max-w-3xl">
        @include('reports.partials.show-responses', ['report' => $report, 'semua' => true])
    </div>
</x-app-layout>
