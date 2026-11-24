<x-app-layout>
    <x-slot name="title">Jadwal Pendampingan</x-slot>

    <x-page-header :title="'Jadwal '.$schedule->jenisLabel()" :back="route('petugas.laporan.show', $schedule->report).'#jadwal'"
                   :subtitle="'Laporan '.$schedule->report->kode_tiket" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="Ubah Jadwal">
                @if ($schedule->isCancelled())
                    <p class="text-sm text-gray-500">Jadwal ini sudah dibatalkan dan tidak dapat diubah.</p>
                @else
                    <form method="POST" action="{{ route('petugas.jadwal.update', $schedule) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        @include('petugas.partials.schedule-fields', ['schedule' => $schedule])
                        <p class="text-xs text-gray-500">Jika tanggal, jam, atau lokasi diubah, pelapor perlu mengonfirmasi ulang.</p>
                        <div class="flex justify-end">
                            <x-btn dusk="update-schedule">Simpan Perubahan</x-btn>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Status">
                <x-badge :color="$schedule->statusColor()" dusk="schedule-status">{{ $schedule->statusLabel() }}</x-badge>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Pendamping</dt>
                        <dd class="font-medium text-gray-900">{{ $schedule->petugas->nama }}</dd>
                    </div>
                    @if ($schedule->dikonfirmasi_at)
                        <div>
                            <dt class="text-gray-500">Dikonfirmasi pelapor</dt>
                            <dd class="font-medium text-gray-900">{{ $schedule->dikonfirmasi_at->translatedFormat('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                @unless ($schedule->isCancelled())
                    <form method="POST" action="{{ route('petugas.jadwal.destroy', $schedule) }}" class="mt-5" onsubmit="return confirm('Batalkan jadwal ini? Pelapor akan menerima pemberitahuan.')">
                        @csrf
                        @method('DELETE')
                        <x-btn variant="danger" size="sm" icon="x" dusk="cancel-schedule">Batalkan Jadwal</x-btn>
                    </form>
                @endunless
            </x-card>
        </div>
    </div>
</x-app-layout>
