<x-app-layout>
    <x-slot name="title">Antrean Laporan</x-slot>

    <x-page-header title="Antrean Laporan" subtitle="Laporan yang masuk dan perlu ditindaklanjuti oleh Satgas PPKPT." />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" dusk="queue-stats">
        <x-stat-card label="Laporan Baru" :value="$stats['diajukan'] ?? 0" icon="inbox" color="red" :href="route('petugas.antrean', ['status' => 'diajukan'])" dusk="stat-baru" />
        <x-stat-card label="Dalam Verifikasi" :value="$stats['dalam_verifikasi'] ?? 0" icon="clock" color="amber" :href="route('petugas.antrean', ['status' => 'dalam_verifikasi'])" />
        <x-stat-card label="Perlu Informasi" :value="$stats['perlu_informasi'] ?? 0" icon="chat" color="purple" :href="route('petugas.antrean', ['status' => 'perlu_informasi'])" />
        <x-stat-card label="Dalam Penanganan" :value="$stats['dalam_penanganan'] ?? 0" icon="shield" color="blue" :href="route('petugas.antrean', ['status' => 'dalam_penanganan'])" />
    </div>

    @includeIf('petugas.partials.upcoming-schedules')

    <x-card :padding="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 lg:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kode tiket..." class="sc-input pl-10" dusk="search">
            </div>
            <select name="category_id" class="sc-input lg:w-56" dusk="filter-category">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->nama_kategori }}</option>
                @endforeach
            </select>
            <select name="status" class="sc-input lg:w-52" dusk="filter-status">
                <option value="">Semua status aktif</option>
                @foreach (\App\Models\Report::STATUSES as $key => $status)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
                @endforeach
            </select>
            <x-btn variant="secondary" dusk="apply-filter">Terapkan</x-btn>
        </form>

        @if ($reports->isEmpty())
            <x-empty-state title="Antrean kosong" icon="inbox">Tidak ada laporan yang cocok dengan filter.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="queue-table">
                    <thead>
                        <tr>
                            <th>Kode Tiket</th>
                            <th>Tanggal Masuk</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            <tr dusk="queue-row-{{ $report->id }}">
                                <td class="font-mono text-xs font-semibold text-gray-900">{{ $report->kode_tiket }}</td>
                                <td class="whitespace-nowrap text-gray-500">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $report->category->nama_kategori }}</td>
                                <td><x-badge :color="$report->statusColor()">{{ $report->statusLabel() }}</x-badge></td>
                                <td class="text-right">
                                    <x-btn variant="secondary" size="sm" :href="route('petugas.laporan.show', $report)" dusk="open-{{ $report->id }}">Lihat Detail</x-btn>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $reports->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
