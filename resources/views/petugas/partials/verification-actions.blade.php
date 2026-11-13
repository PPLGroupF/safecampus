{{-- PBI-09: Verifikasi dan perubahan status laporan oleh petugas. --}}
@if (Route::has('petugas.laporan.verify'))
    <div class="mb-6" x-data="{ action: null }" dusk="verification-panel">
        @if ($report->status === \App\Models\Report::STATUS_DALAM_VERIFIKASI)
            <div class="sc-card p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold text-gray-900">Keputusan verifikasi</div>
                        <p class="text-sm text-gray-500">Periksa kelengkapan laporan, lalu pilih salah satu keputusan.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-btn type="button" icon="check" x-on:click="action = 'terima'" dusk="choose-terima">Terima</x-btn>
                        <x-btn type="button" variant="secondary" icon="chat" x-on:click="action = 'minta_info'" dusk="choose-minta-info">Minta Informasi</x-btn>
                        <x-btn type="button" variant="danger" icon="x" x-on:click="action = 'tolak'" dusk="choose-tolak">Tolak</x-btn>
                    </div>
                </div>

                <form method="POST" action="{{ route('petugas.laporan.verify', $report) }}" x-show="action" x-cloak class="mt-5 space-y-4 border-t border-gray-100 pt-5">
                    @csrf
                    <input type="hidden" name="keputusan" :value="action">

                    <div class="space-y-1.5">
                        <label for="catatan_verifikasi" class="sc-label">
                            <span x-show="action === 'terima'">Catatan untuk pelapor (opsional)</span>
                            <span x-show="action === 'minta_info'">Informasi yang dibutuhkan dari pelapor <span class="text-brand-600">*</span></span>
                            <span x-show="action === 'tolak'">Alasan penolakan yang dapat dibaca pelapor <span class="text-brand-600">*</span></span>
                        </label>
                        <textarea id="catatan_verifikasi" name="catatan" rows="3" maxlength="1000" class="sc-input" dusk="verification-note">{{ old('catatan') }}</textarea>
                        <p class="text-xs text-gray-500">Catatan ini tampil pada timeline pelapor. Untuk catatan rahasia Satgas, gunakan Catatan Internal.</p>
                        @error('catatan')<p class="text-xs font-medium text-red-600" dusk="error-catatan">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <x-btn type="button" variant="secondary" x-on:click="action = null">Batal</x-btn>
                        <x-btn dusk="submit-verification">Simpan Keputusan</x-btn>
                    </div>
                </form>
            </div>
        @elseif (in_array($report->status, [\App\Models\Report::STATUS_PERLU_INFORMASI, \App\Models\Report::STATUS_DALAM_PENANGANAN], true))
            @php
                $next = $report->status === \App\Models\Report::STATUS_PERLU_INFORMASI
                    ? ['status' => \App\Models\Report::STATUS_DALAM_VERIFIKASI, 'label' => 'Lanjutkan Verifikasi', 'hint' => 'Gunakan setelah pelapor memberikan informasi tambahan.', 'note' => 'Catatan (opsional)']
                    : ['status' => \App\Models\Report::STATUS_SELESAI, 'label' => 'Tutup Kasus (Selesai)', 'hint' => 'Tutup kasus beserta ringkasan tindak lanjut yang dapat dibaca pelapor.', 'note' => 'Ringkasan tindak lanjut *'];
            @endphp
            <div class="sc-card p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold text-gray-900">Perbarui status</div>
                        <p class="text-sm text-gray-500">{{ $next['hint'] }}</p>
                    </div>
                    <x-btn type="button" icon="arrow-right" x-on:click="action = 'status'" dusk="choose-update-status">{{ $next['label'] }}</x-btn>
                </div>

                <form method="POST" action="{{ route('petugas.laporan.status', $report) }}" x-show="action === 'status'" x-cloak class="mt-5 space-y-4 border-t border-gray-100 pt-5">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next['status'] }}">

                    <div class="space-y-1.5">
                        <label for="catatan_status" class="sc-label">{{ $next['note'] }}</label>
                        <textarea id="catatan_status" name="catatan" rows="3" maxlength="1000" class="sc-input" dusk="status-note">{{ old('catatan') }}</textarea>
                        @error('catatan')<p class="text-xs font-medium text-red-600" dusk="error-catatan">{{ $message }}</p>@enderror
                        @error('status')<p class="text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <x-btn type="button" variant="secondary" x-on:click="action = null">Batal</x-btn>
                        <x-btn dusk="submit-status">Simpan</x-btn>
                    </div>
                </form>
            </div>
        @elseif (in_array($report->status, [\App\Models\Report::STATUS_DITOLAK, \App\Models\Report::STATUS_SELESAI], true))
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                <x-icon name="lock" class="h-5 w-5 text-gray-400" />
                Laporan berstatus {{ $report->statusLabel() }}. Status ini adalah status akhir dan tidak dapat diubah lagi.
            </div>
        @endif
    </div>
@endif
