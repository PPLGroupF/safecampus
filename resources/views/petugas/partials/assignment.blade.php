{{-- SAF-410: Penugasan laporan ke petugas dan riwayatnya. --}}
@if (Route::has('petugas.laporan.assign'))
    @php
        $officers = \App\Models\User::query()->active()->where('role', \App\Models\User::ROLE_PETUGAS)->orderBy('nama')->get();
        $history = $report->assignments()->with(['petugas', 'penugas', 'pencabut'])->get();
        $isFinal = in_array($report->status, [\App\Models\Report::STATUS_DITOLAK, \App\Models\Report::STATUS_SELESAI, \App\Models\Report::STATUS_DIBATALKAN], true);
        $current = $report->petugas;
    @endphp

    <x-card title="Penugasan" x-data="{ open: {{ $errors->has('petugas_id') || $errors->has('alasan') ? 'true' : 'false' }} }">
        <div class="flex items-center gap-3" dusk="assignment-current">
            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $current ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                <x-icon name="user" class="h-5 w-5" />
            </div>
            <div>
                <div class="text-xs text-gray-500">Petugas penanggung jawab</div>
                <div class="font-semibold text-gray-900" dusk="assigned-officer">{{ $current?->nama ?? 'Belum ditugaskan' }}</div>
            </div>
        </div>

        @unless ($isFinal)
            <div class="mt-4 flex flex-wrap gap-2">
                <x-btn type="button" variant="secondary" size="sm" icon="swap" x-on:click="open = ! open" dusk="toggle-assignment">
                    {{ $current ? 'Alihkan Penugasan' : 'Tugaskan Petugas' }}
                </x-btn>
                @if ($current)
                    <form method="POST" action="{{ route('petugas.laporan.unassign', $report) }}"
                          onsubmit="return confirm('Cabut penugasan? Laporan akan kembali ke antrean bersama.')">
                        @csrf
                        @method('DELETE')
                        <x-btn variant="danger" size="sm" icon="x" dusk="revoke-assignment">Cabut Penugasan</x-btn>
                    </form>
                @endif
            </div>

            <form method="POST" action="{{ $current ? route('petugas.laporan.reassign', $report) : route('petugas.laporan.assign', $report) }}"
                  x-show="open" x-cloak class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                @csrf
                @if ($current)
                    @method('PATCH')
                @endif

                <div class="space-y-1.5">
                    <label for="petugas_id" class="sc-label">Petugas</label>
                    <select id="petugas_id" name="petugas_id" class="sc-input" dusk="assignment-officer">
                        <option value="">Pilih petugas</option>
                        @foreach ($officers as $officer)
                            <option value="{{ $officer->id }}" @selected(old('petugas_id') == $officer->id) @disabled($current?->id === $officer->id)>
                                {{ $officer->nama }}{{ $officer->is(auth()->user()) ? ' (saya)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('petugas_id')<p class="text-xs font-medium text-red-600" dusk="error-petugas_id">{{ $message }}</p>@enderror
                </div>

                @if ($current)
                    <div class="space-y-1.5">
                        <label for="alasan" class="sc-label">Alasan pengalihan <span class="text-brand-600">*</span></label>
                        <textarea id="alasan" name="alasan" rows="2" maxlength="255" class="sc-input" dusk="assignment-reason">{{ old('alasan') }}</textarea>
                        @error('alasan')<p class="text-xs font-medium text-red-600" dusk="error-alasan">{{ $message }}</p>@enderror
                    </div>
                @endif

                <p class="text-xs text-gray-500">Setelah ditugaskan, laporan hanya dapat dibuka oleh petugas penanggung jawab.</p>

                <div class="flex justify-end">
                    <x-btn size="sm" dusk="submit-assignment">Simpan Penugasan</x-btn>
                </div>
            </form>
        @endunless

        @if ($history->isNotEmpty())
            <div class="mt-5 border-t border-gray-100 pt-4">
                <div class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500">Riwayat penugasan</div>
                <ol class="space-y-3 text-sm" dusk="assignment-history">
                    @foreach ($history as $item)
                        <li class="flex gap-2">
                            <span @class(['mt-1.5 h-2 w-2 shrink-0 rounded-full', 'bg-blue-600' => $item->aktif, 'bg-gray-300' => ! $item->aktif])></span>
                            <div>
                                <div class="font-medium text-gray-900">{{ $item->petugas->nama }} @if ($item->aktif)<x-badge color="blue">Aktif</x-badge>@endif</div>
                                <div class="text-xs text-gray-500">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }} · oleh {{ $item->penugas?->nama ?? 'Sistem' }}
                                </div>
                                @if ($item->isRevoked())
                                    <div class="mt-0.5 text-xs text-red-600">Dicabut {{ $item->dicabut_at->translatedFormat('d M Y, H:i') }} oleh {{ $item->pencabut?->nama ?? 'Sistem' }}</div>
                                @endif
                                @if ($item->alasan)
                                    <div class="mt-0.5 text-xs text-gray-600">Alasan: {{ $item->alasan }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </x-card>
@endif
