<x-app-layout>
    <x-slot name="title">Tambah Kategori</x-slot>

    <x-page-header title="Tambah Kategori" :back="route('admin.categories.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @include('admin.categories._form')
        </form>
    </x-card>
</x-app-layout>
