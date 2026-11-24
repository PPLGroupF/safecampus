{{-- SAF-424: Jadwal pendampingan (sisi petugas). --}}
@if (Route::has('petugas.jadwal.store'))
    @php
        $schedules = \App\Models\Schedule::forReport($report)->with('petugas')->get();
        $canSchedule = $report->status === \App\Models\Report::STATUS_DALAM_PENANGANAN;
    @endphp

    <x-card title="Jadwal Pendampingan" subtitle="Jadwal yang dibuat akan tampil di halaman laporan pelapor." id="jadwal" x-data="{ open: {{ $errors->hasAny(['jenis', 'waktu', 'lokasi']) ? 'true' : 'false' }} }" dusk="schedules">
        @if ($schedules->isEmpty())
            <p class="text-sm text-gray-500" dusk="schedules-empty">Belum ada jadwal pendampingan.</p>
        @else
            <ul class="divide-y divide-gray-100" dusk="schedule-list">
                @foreach ($schedules as $schedule)
                    <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between" dusk="schedule-{{ $schedule->id }}">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                                <x-icon name="calendar" class="h-5 w-5" />
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $schedule->jenisLabel() }}</div>
                                <div class="text-sm text-gray-600">{{ $schedule->waktu->translatedFormat('l, d M Y · H:i') }} · {{ $schedule->lokasi }}</div>
                                <div class="text-xs text-gray-500">Pendamping: {{ $schedule->petugas->nama }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :color="$schedule->statusColor()" dusk="schedule-status-{{ $schedule->id }}">{{ $schedule->statusLabel() }}</x-badge>
                            <x-btn variant="secondary" size="sm" :href="route('petugas.jadwal.show', $schedule)" dusk="open-schedule-{{ $schedule->id }}">Detail</x-btn>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canSchedule)
            <div class="mt-4">
                <x-btn type="button" variant="secondary" size="sm" icon="plus" x-on:click="open = ! open" dusk="new-schedule">Buat Jadwal</x-btn>
            </div>
            <form method="POST" action="{{ route('petugas.jadwal.store', $report) }}" x-show="open" x-cloak class="mt-4 space-y-4 border-t border-gray-100 pt-4">
                @csrf
                @include('petugas.partials.schedule-fields')
                <div class="flex justify-end">
                    <x-btn size="sm" dusk="submit-schedule">Simpan Jadwal</x-btn>
                </div>
            </form>
        @else
            <p class="mt-4 text-xs text-gray-500">Jadwal pendampingan dapat dibuat setelah laporan berstatus Dalam Penanganan.</p>
        @endif
    </x-card>
@endif
