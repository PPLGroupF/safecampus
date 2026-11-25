{{--
    Grafik batang horizontal sederhana (tanpa library JavaScript).
    $items: daftar ['label' => string, 'jumlah' => ?int, 'disembunyikan' => bool]
    Kelompok yang disembunyikan (jumlah kurang dari batas) ditampilkan sebagai "<5" tanpa angka asli.
--}}
@php
    $max = max(1, collect($items)->max(fn ($item) => $item['jumlah'] ?? 0));
    $bar = $bar ?? 'bg-brand-600';
@endphp

@if (empty($items))
    <p class="text-sm text-gray-500">Belum ada data pada periode ini.</p>
@else
    <ul class="space-y-3" dusk="{{ $dusk }}">
        @foreach ($items as $item)
            <li dusk="{{ $dusk }}-row">
                <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                    <span class="truncate text-gray-700">{{ $item['label'] }}</span>
                    <span class="shrink-0 font-semibold {{ ($item['disembunyikan'] ?? false) ? 'text-gray-400' : 'text-gray-900' }}" dusk="{{ $dusk }}-value">
                        {{ ($item['disembunyikan'] ?? false) ? '<'.$batas : $item['jumlah'] }}
                    </span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                    @if ($item['disembunyikan'] ?? false)
                        <div class="h-full w-full bg-[repeating-linear-gradient(45deg,#e5e7eb_0,#e5e7eb_4px,#f9fafb_4px,#f9fafb_8px)]"></div>
                    @else
                        <div class="h-full rounded-full {{ $bar }}" style="width: {{ round(($item['jumlah'] / $max) * 100) }}%"></div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
