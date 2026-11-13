@php($pelapor = $report->pelaporUntuk(auth()->user()))

<x-card title="Informasi Pelapor">
    <div class="flex items-center gap-3" dusk="reporter-info">
        <div class="flex h-11 w-11 items-center justify-center rounded-full {{ $pelapor['anonim'] ? 'bg-gray-100 text-gray-500' : 'bg-brand-100 text-brand-700' }}">
            <x-icon :name="$pelapor['anonim'] ? 'lock' : 'user'" />
        </div>
        <div>
            <div class="font-semibold text-gray-900" dusk="reporter-name">{{ $pelapor['nama'] }}</div>
            <div class="text-xs text-gray-500">{{ $report->modeLabel() }}</div>
        </div>
    </div>

    <dl class="mt-4 space-y-3 text-sm">
        @unless ($pelapor['anonim'])
            <div>
                <dt class="text-gray-500">NIM</dt>
                <dd class="font-medium text-gray-900">{{ $pelapor['nim'] ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium text-gray-900 break-all">{{ $pelapor['email'] }}</dd>
            </div>
        @endunless
        <div>
            <dt class="text-gray-500">Unit asal</dt>
            <dd class="font-medium text-gray-900">{{ $pelapor['unit'] ?? '-' }}</dd>
        </div>
    </dl>

    @if ($pelapor['anonim'])
        <p class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
            Pelapor memilih mode Anonim ke Petugas. Identitas disembunyikan di semua tampilan petugas dan admin.
        </p>
    @endif
</x-card>
