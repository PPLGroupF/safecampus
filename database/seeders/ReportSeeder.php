<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Support\TicketCode;
use Illuminate\Database\Seeder;

/**
 * Contoh laporan dummy. Semua isi adalah karangan untuk keperluan demo.
 */
class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor = User::firstWhere('email', 'mahasiswa@safecampus.test');
        $aisyah = User::firstWhere('email', 'aisyah@safecampus.test');

        if (! $pelapor || Report::exists()) {
            return;
        }

        $samples = [
            [$pelapor, 'Perundungan', 'teridentifikasi', 'mahasiswa', 'Data dummy: Sejak awal semester saya sering diejek dan dikucilkan oleh beberapa teman satu angkatan di grup kelas dan saat praktikum di laboratorium.'],
            [$pelapor, 'Kekerasan Berbasis Gender Online', 'anonim', 'tidak_diketahui', 'Data dummy: Saya menerima pesan bernada melecehkan dari akun tanpa nama di media sosial setelah mengunggah foto kegiatan himpunan.'],
            [$aisyah, 'Kekerasan Psikis', 'anonim', 'dosen', 'Data dummy: Saat bimbingan, saya beberapa kali dibentak dan direndahkan di depan mahasiswa lain sehingga saya takut datang ke kampus.'],
            [$pelapor, 'Diskriminasi dan Intoleransi', 'teridentifikasi', 'tenaga_kependidikan', 'Data dummy: Saya diperlakukan berbeda saat mengurus administrasi karena latar belakang saya dan permohonan saya sengaja diperlambat.'],
            [$aisyah, 'Kekerasan Seksual', 'anonim', 'mahasiswa', 'Data dummy: Seorang kakak tingkat mengirim komentar bernada seksual berulang kali melalui pesan pribadi setelah kegiatan organisasi.'],
        ];

        // Alur status contoh agar dashboard dan timeline langsung terisi saat demo.
        $alur = [
            [],
            [],
            [],
            [[Report::STATUS_DALAM_VERIFIKASI, null, 'Laporan diterima sistem dan mulai diperiksa petugas Satgas.'], [Report::STATUS_DALAM_PENANGANAN, 'petugas', 'Laporan valid dan akan ditindaklanjuti.']],
            [[Report::STATUS_DALAM_VERIFIKASI, null, 'Laporan diterima sistem dan mulai diperiksa petugas Satgas.'], [Report::STATUS_DALAM_PENANGANAN, 'petugas', 'Laporan valid dan akan ditindaklanjuti.'], [Report::STATUS_SELESAI, 'petugas', 'Data dummy: pendampingan psikologis telah dilakukan dan kasus diteruskan ke pimpinan fakultas.']],
        ];
        $petugas = User::firstWhere('email', 'petugas@safecampus.test');

        foreach ($samples as $index => [$user, $kategori, $mode, $peran, $kronologi]) {
            if (! $user) {
                continue;
            }

            $report = Report::create([
                'user_id' => $user->id,
                'unit_id' => $user->unit_id,
                'category_id' => Category::firstWhere('nama_kategori', $kategori)?->id ?? Category::first()->id,
                'kode_tiket' => TicketCode::generate(),
                'kronologi' => $kronologi,
                'mode_identitas' => $mode,
                'terlapor_peran' => $peran,
                'terlapor_keterangan' => 'Data dummy: keterangan terlapor.',
                'status' => Report::STATUS_DIAJUKAN,
            ]);

            $report->statuses()->create([
                'status_baru' => Report::STATUS_DIAJUKAN,
                'catatan' => 'Laporan berhasil dibuat dan menunggu verifikasi petugas.',
            ]);

            foreach ($alur[$index] ?? [] as [$status, $oleh, $catatan]) {
                $report->changeStatus($status, $oleh ? $petugas : null, $catatan);
            }
        }
    }
}
