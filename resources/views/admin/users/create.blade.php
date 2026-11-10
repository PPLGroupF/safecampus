<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    <x-page-header title="Tambah Pengguna" :back="route('admin.users.index')" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @include('admin.users._form')
        </form>
    </x-card>
</x-app-layout>
