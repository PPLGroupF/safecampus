<x-app-layout>
    <x-slot name="title">Tambah Kampus</x-slot>

    <x-page-header title="Tambah Kampus" :back="route('admin.campuses.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.campuses.store') }}">
            @include('admin.campuses._form')
        </form>
    </x-card>
</x-app-layout>
