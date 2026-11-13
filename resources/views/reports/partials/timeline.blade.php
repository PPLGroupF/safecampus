{{-- Timeline status laporan. Dipakai di halaman pelapor dan petugas. --}}
<ol class="relative space-y-6 border-l border-gray-200 pl-6" dusk="status-timeline">
    @foreach ($report->statuses as $history)
        <li class="relative">
            <span @class([
                'absolute -left-[31px] top-1 flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white',
                'bg-brand-600' => $loop->last,
                'bg-gray-300' => ! $loop->last,
            ])></span>
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :color="$history->statusColor()">{{ $history->statusLabel() }}</x-badge>
                <span class="text-xs text-gray-500">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</span>
            </div>
            @if ($history->catatan)
                <p class="mt-1.5 text-sm text-gray-700">{{ $history->catatan }}</p>
            @endif
        </li>
    @endforeach
</ol>
