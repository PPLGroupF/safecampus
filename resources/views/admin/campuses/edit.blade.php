<x-app-layout>
    <x-slot name="title">Ubah Kampus</x-slot>

    <x-page-header title="Ubah Kampus" :subtitle="$campus->nama_kampus" :back="route('admin.campuses.index')" />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.campuses.update', $campus) }}">
            @method('PUT')
            @include('admin.campuses._form')
        </form>
    </x-card>
</x-app-layout>
