<x-app-layout>
    <x-slot name="title">Panduan Pelaporan</x-slot>

    <x-page-header title="Panduan Pelaporan" subtitle="Ketahui harus melapor ke mana, apa yang perlu disiapkan, dan apa yang terjadi setelah melapor." />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card title="Ke mana saya melapor?">
                <div class="space-y-3 text-sm leading-relaxed text-gray-700">
                    <p>SafeCampus adalah satu pintu pelaporan kekerasan berbasis gender di lingkungan Telkom University. Laporan Anda diterima dan ditangani oleh <strong>Satgas PPKPT Telkom University</strong>, sesuai Permendikbudristek Nomor 55 Tahun 2024.</p>
                    <p>Kanal lain seperti posko Satgas di Gedung Bangkit tetap bisa digunakan. SafeCampus membantu Anda memantau laporan melalui kode tiket dan timeline status.</p>
                </div>
            </x-card>

            <x-card title="Langkah melapor" dusk="guide-steps">
                <ol class="space-y-5">
                    @foreach ([
                        ['Siapkan informasi', 'Ingat kembali waktu, tempat, dan urutan kejadian. Kumpulkan bukti jika ada, misalnya tangkapan layar atau foto.'],
                        ['Isi formulir 4 langkah', 'Tulis kronologi dan pilih kategori, lampirkan bukti, isi data terlapor, lalu pilih mode identitas.'],
                        ['Simpan kode tiket', 'Setelah laporan terkirim, Anda mendapat kode tiket untuk memantau laporan di menu Laporan Saya.'],
                        ['Pantau status', 'Petugas memverifikasi laporan. Status berubah menjadi Dalam Penanganan, Perlu Informasi, atau Ditolak beserta alasannya.'],
                        ['Ikuti pendampingan', 'Jika laporan ditangani, petugas dapat menjadwalkan pendampingan yang bisa Anda konfirmasi.'],
                    ] as [$judul, $isi])
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">{{ $loop->iteration }}</span>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $judul }}</div>
                                <p class="mt-0.5 text-sm text-gray-600">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </x-card>

            <x-card title="Arti status laporan">
                <dl class="grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        'diajukan' => 'Laporan baru diterima dan menunggu diperiksa petugas. Masih dapat Anda ubah atau batalkan.',
                        'dalam_verifikasi' => 'Petugas sedang memeriksa kelengkapan laporan.',
                        'perlu_informasi' => 'Petugas membutuhkan informasi tambahan dari Anda.',
                        'dalam_penanganan' => 'Laporan valid dan sedang ditindaklanjuti, termasuk pendampingan.',
                        'selesai' => 'Kasus ditutup dengan ringkasan tindak lanjut.',
                        'ditolak' => 'Laporan tidak memenuhi syarat. Alasannya ditampilkan kepada Anda.',
                        'dibatalkan' => 'Laporan dibatalkan oleh Anda sebelum diverifikasi.',
                    ] as $status => $arti)
                        <div class="rounded-lg border border-gray-100 p-3">
                            <x-badge :color="\App\Models\Report::STATUSES[$status]['color']">{{ \App\Models\Report::STATUSES[$status]['label'] }}</x-badge>
                            <p class="mt-2 text-sm text-gray-600">{{ $arti }}</p>
                        </div>
                    @endforeach
                </dl>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Hak Anda sebagai pelapor" dusk="guide-rights">
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Identitas dan laporan dijaga kerahasiaannya.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Mendapat informasi perkembangan laporan.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Mendapat pendampingan sesuai kebutuhan.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Dilindungi dari tindakan balasan atas laporan.</li>
                </ul>
            </x-card>

            <x-card title="Teridentifikasi atau Anonim?" dusk="guide-modes">
                <div class="space-y-4 text-sm">
                    <div>
                        <div class="font-semibold text-gray-900">Teridentifikasi</div>
                        <p class="mt-0.5 text-gray-600">Petugas melihat nama, NIM, unit, dan email Anda. Komunikasi dan pendampingan jadi lebih cepat.</p>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Anonim ke Petugas</div>
                        <p class="mt-0.5 text-gray-600">Identitas disembunyikan dari petugas dan admin. Laporan tetap terhubung ke akun Anda agar bisa dipantau, jadi jangan menulis data pribadi di kronologi.</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
