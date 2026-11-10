<x-app-layout>
    <x-slot name="title">Ubah Pengguna</x-slot>

    <x-page-header title="Ubah Pengguna" :subtitle="$user->nama" :back="route('admin.users.index')" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @method('PUT')
            @include('admin.users._form')
        </form>
    </x-card>
</x-app-layout>
