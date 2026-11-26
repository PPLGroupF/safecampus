{{-- SAF-443: Tanggapan pelapor (sisi petugas). --}}
@if (Route::has('tanggapan.store'))
    @php($responses = \App\Models\Response::forReport($report)->with('replies.penulis')->get())

    <x-card title="Tanggapan Pelapor" id="tanggapan" dusk="responses">
        @if ($responses->isEmpty())
            <p class="text-sm text-gray-500" dusk="responses-empty">Pelapor belum mengirim tanggapan.</p>
        @else
            <ul class="space-y-4" dusk="response-list">
                @foreach ($responses as $response)
                    <li dusk="response-{{ $response->id }}">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="text-xs text-gray-500">
                                    <span class="font-semibold text-gray-700">{{ $report->pelaporUntuk(auth()->user())['nama'] }}</span>
                                    · {{ $response->created_at->translatedFormat('d M Y, H:i') }}
                                    @if ($response->updated_at->gt($response->created_at))<span class="italic">(diubah)</span>@endif
                                </div>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-800" dusk="response-text-{{ $response->id }}">{{ $response->pesan }}</p>
                        </div>

                        @foreach ($response->replies as $reply)
                            <div class="ml-6 mt-2 rounded-lg border border-blue-100 bg-blue-50 p-3" dusk="reply-{{ $reply->id }}">
                                <div class="text-xs text-blue-700"><span class="font-semibold">{{ $reply->penulis->nama }}</span> · {{ $reply->created_at->translatedFormat('d M Y, H:i') }}</div>
                                <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $reply->pesan }}</p>
                            </div>
                        @endforeach

                        @includeIf('petugas.partials.response-reply', ['response' => $response])
                    </li>
                @endforeach
            </ul>
        @endif
    </x-card>
@endif
