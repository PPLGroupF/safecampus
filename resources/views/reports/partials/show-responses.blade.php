{{-- SAF-443: Tanggapan pelapor dan balasan petugas (sisi pelapor). --}}
@if (Route::has('tanggapan.store'))
    @php
        // Di detail laporan hanya 3 tanggapan terakhir; semuanya ada di halaman Semua Tanggapan (index).
        $semua ??= false;
        $all = \App\Models\Response::forReport($report)->with('replies')->get();
        $responses = $semua ? $all : $all->slice(-3)->values();
        $isOpen = \App\Models\Response::isOpenFor($report);
    @endphp

    <x-card title="Tanggapan" subtitle="Kirim informasi tambahan atau pertanyaan kepada petugas Satgas." id="tanggapan" dusk="responses">
        @if ($responses->isEmpty())
            <p class="text-sm text-gray-500" dusk="responses-empty">Belum ada tanggapan.</p>
        @else
            <ul class="space-y-4" dusk="response-list">
                @foreach ($responses as $response)
                    @php($bag = $errors->getBag('response'.$response->id))
                    <li x-data="{ editing: {{ $bag->any() ? 'true' : 'false' }} }" dusk="response-{{ $response->id }}">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="text-xs text-gray-500">
                                    <span class="font-semibold text-gray-700">Anda</span>
                                    · {{ $response->created_at->translatedFormat('d M Y, H:i') }}
                                    @if ($response->isRead())
                                        <x-badge color="green" dusk="response-read-{{ $response->id }}">Sudah dibaca petugas</x-badge>
                                    @endif
                                </div>
                                @include('reports.partials.response-actions')
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-800" x-show="! editing" dusk="response-text-{{ $response->id }}">{{ $response->pesan }}</p>

                            <form method="POST" action="{{ route('tanggapan.update', $response) }}" x-show="editing" x-cloak class="mt-2 space-y-2">
                                @csrf
                                @method('PUT')
                                <textarea name="pesan" rows="3" maxlength="2000" class="sc-input" dusk="edit-response-input-{{ $response->id }}">{{ $bag->any() ? old('pesan') : $response->pesan }}</textarea>
                                @if ($bag->has('pesan'))<p class="text-xs font-medium text-red-600">{{ $bag->first('pesan') }}</p>@endif
                                <div class="flex justify-end gap-2">
                                    <x-btn type="button" variant="secondary" size="sm" x-on:click="editing = false">Batal</x-btn>
                                    <x-btn size="sm" dusk="save-response-{{ $response->id }}">Simpan</x-btn>
                                </div>
                            </form>
                        </div>

                        @foreach ($response->replies as $reply)
                            <div class="ml-6 mt-2 rounded-lg border border-blue-100 bg-blue-50 p-3" dusk="reply-{{ $reply->id }}">
                                <div class="text-xs text-blue-700"><span class="font-semibold">Petugas Satgas</span> · {{ $reply->created_at->translatedFormat('d M Y, H:i') }}</div>
                                <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $reply->pesan }}</p>
                            </div>
                        @endforeach
                    </li>
                @endforeach
            </ul>

            @if (! $semua && $all->count() > $responses->count())
                <a href="{{ route('tanggapan.index', $report) }}" class="mt-4 inline-block text-sm font-semibold text-brand-700 hover:underline" dusk="all-responses">
                    Lihat semua tanggapan ({{ $all->count() }})
                </a>
            @endif
        @endif

        @if ($isOpen)
            <form method="POST" action="{{ route('tanggapan.store', $report) }}" class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                @csrf
                <label for="pesan" class="sc-label">Tulis tanggapan</label>
                <textarea id="pesan" name="pesan" rows="3" maxlength="2000" class="sc-input" dusk="response-input">{{ old('pesan') }}</textarea>
                @error('pesan')<p class="text-xs font-medium text-red-600" dusk="error-pesan">{{ $message }}</p>@enderror
                <div class="flex justify-end">
                    <x-btn size="sm" icon="chat" dusk="submit-response">Kirim Tanggapan</x-btn>
                </div>
            </form>
        @else
            <p class="mt-4 text-xs text-gray-500" dusk="responses-closed">Tanggapan dapat dikirim saat laporan berstatus Dalam Verifikasi, Perlu Informasi, atau Dalam Penanganan.</p>
        @endif
    </x-card>
@endif
