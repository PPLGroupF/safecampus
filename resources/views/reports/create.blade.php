<x-app-layout>
    <x-slot name="title">Buat Laporan</x-slot>

    <x-page-header title="Buat Laporan" subtitle="Isi laporan dengan tenang. Data Anda diproses secara rahasia oleh Satgas PPKPT." />

    <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" novalidate>
        @include('reports._form', ['submitLabel' => 'Kirim Laporan'])
    </form>
</x-app-layout>
