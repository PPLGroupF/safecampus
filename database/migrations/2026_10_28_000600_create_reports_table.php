<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // Pelapor tetap terhubung ke akun walaupun memilih mode anonim (untuk pemantauan dan notifikasi).
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Petugas penanggung jawab saat ini; kosong saat laporan baru diajukan.
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('kode_tiket', 50)->unique();
            $table->text('kronologi');
            $table->enum('mode_identitas', ['teridentifikasi', 'anonim'])->default('teridentifikasi');
            $table->enum('terlapor_peran', ['mahasiswa', 'dosen', 'tenaga_kependidikan', 'petugas_satgas', 'lainnya', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->text('terlapor_keterangan')->nullable();
            $table->enum('status', ['diajukan', 'dalam_verifikasi', 'perlu_informasi', 'ditolak', 'dalam_penanganan', 'selesai', 'dibatalkan'])->default('diajukan');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('report_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            // Kosong jika perubahan dilakukan oleh sistem.
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_baru', ['diajukan', 'dalam_verifikasi', 'perlu_informasi', 'ditolak', 'dalam_penanganan', 'selesai', 'dibatalkan']);
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_statuses');
        Schema::dropIfExists('reports');
    }
};
