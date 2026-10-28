<x-app-layout>
    <x-slot name="title">Beranda</x-slot>

    <x-page-header :title="'Halo, '.auth()->user()->nama" subtitle="Selamat datang di SafeCampus." />

    <x-card>
        <x-empty-state title="Fitur sedang disiapkan" icon="shield">
            Anda masuk sebagai {{ auth()->user()->roleLabel() }}. Menu akan muncul di sidebar setelah fiturnya tersedia.
        </x-empty-state>
    </x-card>
</x-app-layout>
