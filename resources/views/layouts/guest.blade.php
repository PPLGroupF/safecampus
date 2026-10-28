<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SafeCampus') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            <div class="relative hidden overflow-hidden bg-brand-700 lg:flex lg:flex-col lg:justify-between lg:p-12">
                <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-brand-600/60"></div>
                <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-brand-800/60"></div>

                <div class="relative flex items-center gap-3 text-white">
                    <div class="rounded-xl bg-white p-1.5"><x-application-logo class="h-9 w-9" /></div>
                    <div class="text-xl font-bold">SafeCampus</div>
                </div>

                <div class="relative max-w-md text-white">
                    <h2 class="text-3xl font-bold leading-tight">Melapor dengan aman, memantau dengan tenang.</h2>
                    <p class="mt-4 text-brand-100">
                        Satu pintu pelaporan kekerasan berbasis gender bagi mahasiswa Telkom University,
                        ditangani langsung oleh Satgas PPKPT. Identitas pelapor dapat disembunyikan dari petugas.
                    </p>
                </div>

                <p class="relative text-xs text-brand-200">Prototipe tugas besar PPL · Semua data adalah data dummy.</p>
            </div>

            <div class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-6 py-12 lg:min-h-0">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <x-application-logo class="h-10 w-10" />
                    <div class="text-xl font-bold">Safe<span class="text-brand-600">Campus</span></div>
                </div>

                <div class="w-full max-w-md sc-card p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
