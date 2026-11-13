{{-- Aksi pelapor: hanya tersedia selama laporan belum diverifikasi. --}}
@if ($report->isEditableByReporter())
    <x-btn variant="secondary" icon="pencil" :href="route('laporan.edit', $report)" dusk="edit-report">Ubah Laporan</x-btn>

    <form method="POST" action="{{ route('laporan.cancel', $report) }}"
          onsubmit="return confirm('Batalkan laporan ini? Laporan yang dibatalkan tidak dapat diaktifkan kembali.')">
        @csrf
        @method('PATCH')
        <x-btn variant="danger" icon="ban" dusk="cancel-report">Batalkan</x-btn>
    </form>
@endif
