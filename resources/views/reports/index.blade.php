<x-app-layout>
    <x-slot name="title">Laporan Saya</x-slot>

    <x-page-header title="Laporan Saya" subtitle="Daftar laporan yang pernah Anda buat beserta status terbarunya.">
        <x-slot name="actions">
            <x-btn :href="route('laporan.create')" icon="plus" dusk="new-report">Buat Laporan</x-btn>
        </x-slot>
    </x-page-header>

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
            <select name="status" class="sc-input lg:w-48" dusk="filter-status">
                <option value="">Semua status</option>
                @foreach (\App\Models\Report::STATUSES as $key => $status)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
                @endforeach
            </select>
            <x-btn variant="secondary" dusk="apply-filter">Terapkan</x-btn>
        </form>

        @if ($reports->isEmpty())
            <x-empty-state title="Belum ada laporan" icon="document">
                Laporan yang Anda buat akan muncul di sini beserta statusnya.
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="report-table">
                    <thead>
                        <tr>
                            <th>Kode Tiket</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Mode</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            <tr dusk="report-row-{{ $report->id }}">
                                <td class="font-mono text-xs font-semibold text-gray-900">{{ $report->kode_tiket }}</td>
                                <td class="whitespace-nowrap text-gray-500">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $report->category->nama_kategori }}</td>
                                <td class="text-gray-600">{{ $report->modeLabel() }}</td>
                                <td><x-badge :color="$report->statusColor()">{{ $report->statusLabel() }}</x-badge></td>
                                <td class="text-right">
                                    <x-btn variant="secondary" size="sm" :href="route('laporan.show', $report)" dusk="detail-{{ $report->id }}">Detail</x-btn>
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
