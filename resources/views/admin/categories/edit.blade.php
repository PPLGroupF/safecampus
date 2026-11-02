<x-app-layout>
    <x-slot name="title">Ubah Kategori</x-slot>

    <x-page-header title="Ubah Kategori" :subtitle="$category->nama_kategori" :back="route('admin.categories.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </x-card>
</x-app-layout>
