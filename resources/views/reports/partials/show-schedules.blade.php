{{-- SAF-424: Jadwal pendampingan (sisi pelapor) dan konfirmasi kehadiran. --}}
@if (Route::has('laporan.jadwal.konfirmasi'))
    @php($schedules = \App\Models\Schedule::forReport($report)->get())

    @if ($schedules->isNotEmpty())
        <x-card title="Jadwal Pendampingan" subtitle="Konfirmasi kehadiran Anda pada jadwal yang dibuat petugas Satgas." dusk="reporter-schedules">
            <ul class="divide-y divide-gray-100">
                @foreach ($schedules as $schedule)
                    <li class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between" dusk="reporter-schedule-{{ $schedule->id }}">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                                <x-icon name="calendar" class="h-5 w-5" />
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $schedule->jenisLabel() }}</div>
                                <div class="text-sm text-gray-600">{{ $schedule->waktu->translatedFormat('l, d M Y · H:i') }}</div>
                                <div class="text-sm text-gray-600">{{ $schedule->lokasi }}</div>
                                @if ($schedule->catatan)
                                    <p class="mt-1 text-xs text-gray-500">{{ $schedule->catatan }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :color="$schedule->statusColor()" dusk="reporter-schedule-status-{{ $schedule->id }}">{{ $schedule->statusLabel() }}</x-badge>
                            @if ($schedule->canBeConfirmed())
                                <form method="POST" action="{{ route('laporan.jadwal.konfirmasi', $schedule) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-btn size="sm" icon="check" dusk="confirm-schedule-{{ $schedule->id }}">Konfirmasi Hadir</x-btn>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>
    @endif
@endif
