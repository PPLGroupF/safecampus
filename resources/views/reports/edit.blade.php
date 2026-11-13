<x-app-layout>
    <x-slot name="title">Ubah Laporan</x-slot>

    <x-page-header :title="'Ubah Laporan '.$report->kode_tiket" :back="route('laporan.show', $report)"
                   subtitle="Laporan hanya dapat diubah selama belum diverifikasi petugas." />

    <form method="POST" action="{{ route('laporan.update', $report) }}" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @include('reports._form', ['submitLabel' => 'Simpan Perubahan'])
    </form>
</x-app-layout>
