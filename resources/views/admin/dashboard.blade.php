<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-page-header title="Dashboard" subtitle="Ringkasan laporan dalam bentuk agregat. Tidak ada identitas pelapor maupun isi laporan yang ditampilkan." />

    <form method="GET" class="sc-card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end" dusk="dashboard-filter">
        <div class="space-y-1.5">
            <label for="dari" class="sc-label">Dari tanggal</label>
            <input id="dari" type="date" name="dari" value="{{ $data['periode']['dari'] }}" class="sc-input" dusk="filter-dari">
        </div>
        <div class="space-y-1.5">
            <label for="sampai" class="sc-label">Sampai tanggal</label>
            <input id="sampai" type="date" name="sampai" value="{{ $data['periode']['sampai'] }}" class="sc-input" dusk="filter-sampai">
        </div>
        <div class="flex gap-2">
            <x-btn dusk="apply-filter">Terapkan</x-btn>
            <x-btn variant="secondary" :href="route('admin.dashboard')">Semua periode</x-btn>
        </div>
        @error('sampai')<p class="text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" dusk="dashboard-summary">
        <x-stat-card label="Total laporan" :value="$data['ringkasan']['total']" icon="document" color="gray" dusk="stat-total" />
        <x-stat-card label="Sedang diproses" :value="$data['ringkasan']['aktif']" icon="clock" color="amber" dusk="stat-aktif" />
        <x-stat-card label="Selesai" :value="$data['ringkasan']['selesai']" icon="check" color="green" dusk="stat-selesai" />
        <x-stat-card label="Ditolak" :value="$data['ringkasan']['ditolak']" icon="x" color="red" dusk="stat-ditolak" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Laporan per Status">
            @include('admin.partials.bar-chart', ['items' => $data['per_status'], 'batas' => $data['batas'], 'dusk' => 'chart-status', 'bar' => 'bg-blue-600'])
        </x-card>

        <x-card title="Tren 6 Bulan Terakhir" subtitle="Jumlah laporan masuk per bulan.">
            @include('admin.partials.bar-chart', ['items' => $data['per_bulan'], 'batas' => $data['batas'], 'dusk' => 'chart-bulan', 'bar' => 'bg-gray-700'])
        </x-card>

        <x-card title="Laporan per Kategori">
            @include('admin.partials.bar-chart', ['items' => $data['per_kategori'], 'batas' => $data['batas'], 'dusk' => 'chart-kategori'])
        </x-card>

        <x-card title="Laporan per Unit Asal Pelapor">
            @include('admin.partials.bar-chart', ['items' => $data['per_unit'], 'batas' => $data['batas'], 'dusk' => 'chart-unit', 'bar' => 'bg-purple-600'])
        </x-card>
    </div>

    <p class="mt-6 flex items-start gap-2 text-xs text-gray-500" dusk="suppression-note">
        <x-icon name="lock" class="mt-0.5 h-4 w-4 shrink-0" />
        Kelompok kategori atau unit dengan jumlah laporan kurang dari {{ $data['batas'] }} ditampilkan sebagai "&lt;{{ $data['batas'] }}" agar pelapor tidak dapat dikenali dari jumlah yang kecil.
    </p>
</x-app-layout>
