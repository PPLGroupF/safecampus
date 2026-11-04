<x-app-layout>
    <x-slot name="title">Ubah Unit</x-slot>

    <x-page-header title="Ubah Unit / Fakultas" :subtitle="$unit->nama_unit" :back="route('admin.units.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.units.update', $unit) }}">
            @method('PUT')
            @include('admin.units._form')
        </form>
    </x-card>
</x-app-layout>
