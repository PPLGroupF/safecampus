{{--
    Formulir laporan 4 langkah: 1 Kronologi, 2 Bukti, 3 Data Terlapor, 4 Privasi.
    Dipakai untuk membuat dan mengubah laporan.
--}}
@php
    $stepOfField = ['category_id' => 1, 'kronologi' => 1, 'bukti' => 2, 'bukti.*' => 2, 'terlapor_peran' => 3, 'terlapor_keterangan' => 3, 'mode_identitas' => 4];
    $initialStep = 1;
    foreach ($stepOfField as $field => $step) {
        if ($errors->has($field)) { $initialStep = $step; break; }
    }
    $steps = [1 => 'Kronologi', 2 => 'Bukti', 3 => 'Data Terlapor', 4 => 'Privasi'];
@endphp

@csrf

<div x-data="reportForm({{ $initialStep }})" class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        {{-- Indikator langkah --}}
        <ol class="sc-card flex items-center gap-2 overflow-x-auto p-4" dusk="form-steps">
            @foreach ($steps as $number => $label)
                <li class="flex flex-1 items-center gap-2 whitespace-nowrap">
                    <button type="button" @click="step = {{ $number }}" class="flex items-center gap-2" dusk="step-{{ $number }}">
                        <span :class="step >= {{ $number }} ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-500'"
                              class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold">{{ $number }}</span>
                        <span :class="step === {{ $number }} ? 'text-gray-900' : 'text-gray-500'" class="text-sm font-medium">{{ $label }}</span>
                    </button>
                    @unless ($loop->last)
                        <span class="hidden h-px flex-1 bg-gray-200 sm:block"></span>
                    @endunless
                </li>
            @endforeach
        </ol>

        {{-- Langkah 1: Kronologi --}}
        <x-card data-step="1" x-show="step === 1" title="Kronologi Kejadian" subtitle="Ceritakan kejadian yang Anda alami dengan jelas dan sesuai fakta.">
            <div class="space-y-5">
                <x-form-field label="Kategori kekerasan" name="category_id" required>
                    <select id="category_id" name="category_id" required class="sc-input" dusk="category_id">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $report->category_id) == $category->id)>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Kronologi" name="kronologi" required
                              hint="Tuliskan kapan dan di mana kejadian terjadi, apa yang terjadi, dan siapa saksinya (jika ada). Minimal 30 karakter.">
                    <textarea id="kronologi" name="kronologi" rows="9" minlength="30" maxlength="5000" required class="sc-input" dusk="kronologi"
                              placeholder="Contoh: Pada hari Senin, 12 Oktober 2026 sekitar pukul 10.00 di ruang kelas lantai 2...">{{ old('kronologi', $report->kronologi) }}</textarea>
                </x-form-field>
            </div>
        </x-card>

        {{-- Langkah 2: Bukti --}}
        <x-card data-step="2" x-show="step === 2" x-cloak title="Bukti Pendukung" subtitle="Tangkapan layar, foto, rekaman, atau dokumen yang berkaitan dengan kejadian.">
            @includeIf('reports.partials.form-evidence')

            @unless (view()->exists('reports.partials.form-evidence'))
                <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-600">
                    <x-icon name="paperclip" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
                    <p>Unggah bukti belum tersedia pada versi ini. Anda tetap bisa mengirim laporan tanpa bukti.</p>
                </div>
            @endunless
        </x-card>

        {{-- Langkah 3: Data terlapor --}}
        <x-card data-step="3" x-show="step === 3" x-cloak title="Data Terlapor" subtitle="Informasi ini membantu Satgas menghindari konflik kepentingan saat menugaskan petugas.">
            <div class="space-y-5">
                <x-form-field label="Peran terlapor di kampus" name="terlapor_peran" required>
                    <select id="terlapor_peran" name="terlapor_peran" required class="sc-input" dusk="terlapor_peran">
                        @foreach (\App\Models\Report::TERLAPOR_PERAN as $key => $label)
                            <option value="{{ $key }}" @selected(old('terlapor_peran', $report->terlapor_peran ?? 'tidak_diketahui') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Keterangan terlapor" name="terlapor_keterangan"
                              hint="Opsional. Misalnya hubungan Anda dengan terlapor atau ciri yang membantu identifikasi. Disimpan terenkripsi.">
                    <textarea id="terlapor_keterangan" name="terlapor_keterangan" rows="4" maxlength="1000" class="sc-input" dusk="terlapor_keterangan">{{ old('terlapor_keterangan', $report->terlapor_keterangan) }}</textarea>
                </x-form-field>
            </div>
        </x-card>

        {{-- Langkah 4: Privasi --}}
        <x-card data-step="4" x-show="step === 4" x-cloak title="Mode Identitas" subtitle="Pilih bagaimana identitas Anda terlihat oleh petugas Satgas.">
            @php($mode = old('mode_identitas', $report->mode_identitas ?? 'teridentifikasi'))
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="cursor-pointer rounded-xl border p-4 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50" dusk="mode-teridentifikasi">
                    <input type="radio" name="mode_identitas" value="teridentifikasi" class="text-brand-600 focus:ring-brand-500" @checked($mode === 'teridentifikasi')>
                    <span class="ml-2 font-semibold text-gray-900">Teridentifikasi</span>
                    <p class="mt-2 text-sm text-gray-600">Petugas melihat nama, NIM, unit, dan email Anda. Mempercepat pendampingan.</p>
                </label>
                <label class="cursor-pointer rounded-xl border p-4 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50" dusk="mode-anonim">
                    <input type="radio" name="mode_identitas" value="anonim" class="text-brand-600 focus:ring-brand-500" @checked($mode === 'anonim')>
                    <span class="ml-2 font-semibold text-gray-900">Anonim ke Petugas</span>
                    <p class="mt-2 text-sm text-gray-600">Petugas hanya melihat kode tiket, kategori, kronologi, dan bukti. Anda tampil sebagai "Pelapor Anonim".</p>
                </label>
            </div>
            @error('mode_identitas')<p class="mt-2 text-xs font-medium text-red-600" dusk="error-mode_identitas">{{ $message }}</p>@enderror

            <div class="mt-5 flex items-start gap-3 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
                <x-icon name="warning" class="mt-0.5 h-5 w-5 shrink-0" />
                <p>Mode anonim menyembunyikan identitas Anda di dalam sistem, tetapi laporan tetap terhubung ke akun Anda agar Anda dapat memantau status dan menerima notifikasi. Jika memilih anonim, jangan tuliskan nama atau data pribadi Anda di kronologi dan bukti.</p>
            </div>
        </x-card>

        {{-- Navigasi --}}
        <div class="flex items-center justify-between">
            <x-btn type="button" variant="secondary" icon="arrow-left" x-show="step > 1" x-on:click="step--" dusk="prev-step">Kembali</x-btn>
            <span x-show="step === 1"></span>
            <x-btn type="button" x-show="step < total" x-on:click="next()" dusk="next-step">Lanjut</x-btn>
            <x-btn x-show="step === total" x-cloak icon="check" x-on:click="validateAll($event)" dusk="submit-report">{{ $submitLabel }}</x-btn>
        </div>
    </div>

    <aside class="space-y-4">
        <div class="sc-card border-brand-100 bg-brand-50/50 p-5">
            <div class="flex items-center gap-2 font-semibold text-brand-700">
                <x-icon name="lock" class="h-5 w-5" /> Tips Pelaporan Aman
            </div>
            <ol class="mt-4 space-y-3 text-sm text-gray-700">
                <li><span class="font-semibold">1.</span> Sampaikan informasi yang jelas: waktu, tempat, dan urutan kejadian.</li>
                <li><span class="font-semibold">2.</span> Lampirkan bukti yang relevan jika ada.</li>
                <li><span class="font-semibold">3.</span> Kronologi dan keterangan terlapor disimpan terenkripsi dan hanya dibuka oleh petugas Satgas.</li>
                <li><span class="font-semibold">4.</span> Selama belum diverifikasi, laporan masih bisa Anda ubah atau batalkan.</li>
            </ol>
        </div>
        <div class="sc-card p-5 text-sm text-gray-600">
            <div class="font-semibold text-gray-900">Butuh bantuan segera?</div>
            <p class="mt-1">Jika Anda dalam bahaya, hubungi layanan darurat kampus atau datangi posko Satgas PPKPT di Gedung Bangkit.</p>
        </div>
    </aside>
</div>

<script>
    /*
     * Validasi per langkah. Browser tidak bisa menampilkan pesan error untuk kolom
     * yang sedang tersembunyi, sehingga tanpa ini tombol Kirim terlihat "tidak bereaksi".
     */
    function reportForm(initialStep) {
        return {
            step: initialStep,
            total: 4,
            firstInvalid(n) {
                const card = this.$root.querySelector('[data-step="' + n + '"]');
                return card ? [...card.querySelectorAll('input, select, textarea')].find((field) => ! field.checkValidity()) : null;
            },
            next() {
                const field = this.firstInvalid(this.step);
                if (field) {
                    field.reportValidity();
                    return;
                }
                this.step++;
            },
            validateAll(event) {
                for (let n = 1; n <= this.total; n++) {
                    const field = this.firstInvalid(n);
                    if (field) {
                        event.preventDefault();
                        this.step = n;
                        this.$nextTick(() => field.reportValidity());
                        return;
                    }
                }
            },
        };
    }
</script>
