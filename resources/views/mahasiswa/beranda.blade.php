<x-app-layout>
    <x-slot name="title">Beranda</x-slot>

    <x-page-header :title="'Halo, '.auth()->user()->nama" subtitle="Pantau laporan Anda dan bersama ciptakan lingkungan kampus yang lebih aman." />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" dusk="summary-cards">
        <x-stat-card label="Total Laporan" :value="$summary['total'] ?? 0" icon="document" color="red" hint="Seluruh laporan Anda" dusk="stat-total" />
        <x-stat-card label="Dalam Verifikasi" :value="$summary['verifikasi'] ?? 0" icon="clock" color="amber" hint="Diajukan, diverifikasi, atau perlu informasi" dusk="stat-verifikasi" />
        <x-stat-card label="Dalam Penanganan" :value="$summary['penanganan'] ?? 0" icon="shield" color="blue" hint="Sedang ditindaklanjuti Satgas" dusk="stat-penanganan" />
        <x-stat-card label="Selesai" :value="$summary['selesai'] ?? 0" icon="check" color="green" hint="Kasus sudah ditutup" dusk="stat-selesai" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Laporan Terbaru" :padding="false">
            <x-slot name="actions">
                @if (Route::has('laporan.index'))
                    <a href="{{ route('laporan.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lihat semua</a>
                @endif
            </x-slot>

            @if (($latest ?? collect())->isEmpty())
                <x-empty-state title="Belum ada laporan" icon="document">Laporan yang Anda buat akan tampil di sini.</x-empty-state>
            @else
                <ul class="divide-y divide-gray-100" dusk="latest-reports">
                    @foreach ($latest as $report)
                        <li>
                            <a href="{{ route('laporan.show', $report) }}" class="flex items-center justify-between gap-4 px-5 py-3.5 hover:bg-gray-50">
                                <div class="min-w-0">
                                    <div class="font-mono text-xs font-semibold text-gray-900">{{ $report->kode_tiket }}</div>
                                    <div class="truncate text-sm text-gray-500">{{ $report->category->nama_kategori }} · {{ $report->created_at->translatedFormat('d M Y') }}</div>
                                </div>
                                <x-badge :color="$report->statusColor()">{{ $report->statusLabel() }}</x-badge>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <div class="space-y-6">
            @includeIf('mahasiswa.partials.help')
        </div>
    </div>

    @includeIf('mahasiswa.partials.shortcut')
</x-app-layout>
