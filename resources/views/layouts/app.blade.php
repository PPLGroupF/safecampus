<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'SafeCampus') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
            {{-- Latar gelap saat sidebar terbuka di layar kecil --}}
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"></div>

            @include('layouts.partials.sidebar')

            <div class="flex-1 min-w-0 flex flex-col">
                @include('layouts.partials.topbar')

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    <x-flash />

                    {{ $slot }}
                </main>

                <footer class="px-4 py-4 text-xs text-gray-400 sm:px-6 lg:px-8">
                    SafeCampus · Prototipe tugas besar PPL Kelompok F, Telkom University. Semua data adalah data dummy.
                </footer>
            </div>
        </div>
    </body>
</html>
