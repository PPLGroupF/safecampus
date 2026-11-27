<x-app-layout>
    <x-slot name="title">Ekspor Rekap</x-slot>

    <x-page-header title="Ekspor Rekap Laporan" subtitle="Unduh rekap jumlah laporan per kategori dalam format CSV untuk pelaporan ke pimpinan." />

    <form method="GET" class="sc-card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end" dusk="rekap-filter">
        <div class="space-y-1.5">
            <label for="dari" class="sc-label">Dari tanggal</label>
            <input id="dari" type="date" name="dari" value="{{ $periode['dari'] }}" class="sc-input" dusk="filter-dari">
        </div>
        <div class="space-y-1.5">
            <label for="sampai" class="sc-label">Sampai tanggal</label>
            <input id="sampai" type="date" name="sampai" value="{{ $periode['sampai'] }}" class="sc-input" dusk="filter-sampai">
        </div>
        <div class="flex gap-2">
            <x-btn variant="secondary" dusk="apply-filter">Tampilkan</x-btn>
            <x-btn icon="download" :href="route('admin.rekap.export', array_filter($periode))" dusk="download-csv">Unduh CSV</x-btn>
        </div>
        @error('sampai')<p class="text-xs font-medium text-red-600" dusk="error-sampai">{{ $message }}</p>@enderror
    </form>

    <x-card title="Pratinjau Rekap" :subtitle="'Periode: '.($periode['dari'] ?? 'awal').' s.d. '.($periode['sampai'] ?? 'sekarang')" :padding="false">
        @if (empty($rows))
            <x-empty-state title="Belum ada laporan" icon="chart">Tidak ada laporan pada periode yang dipilih.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="rekap-table">
                    <thead>
                        <tr>
                            @foreach ($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($rows as $row)
                            <tr dusk="rekap-row">
                                @foreach ($row as $index => $cell)
                                    <td @class(['font-medium text-gray-900' => $index === 0, 'text-gray-400' => $cell === '<5']) dusk="{{ $index === 0 ? 'rekap-label' : 'rekap-cell' }}">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <p class="mt-6 flex items-start gap-2 text-xs text-gray-500" dusk="rekap-note">
        <x-icon name="lock" class="mt-0.5 h-4 w-4 shrink-0" />
        File CSV hanya berisi jumlah laporan. Tidak ada kode tiket, identitas pelapor, maupun isi laporan.
    </p>
</x-app-layout>
