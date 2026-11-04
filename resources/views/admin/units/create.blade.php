<x-app-layout>
    <x-slot name="title">Tambah Unit</x-slot>

    <x-page-header title="Tambah Unit / Fakultas" :back="route('admin.units.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.units.store') }}">
            @include('admin.units._form')
        </form>
    </x-card>
</x-app-layout>
