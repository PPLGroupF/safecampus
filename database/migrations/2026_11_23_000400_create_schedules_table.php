<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jadwal pendampingan yang dibuat petugas untuk pelapor.
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            // Petugas yang membuat jadwal dan akan mendampingi.
            $table->foreignId('petugas_id')->constrained('users')->restrictOnDelete();
            $table->enum('jenis', ['konseling', 'pendampingan_psikologis', 'pendampingan_hukum', 'pemeriksaan', 'lainnya']);
            $table->dateTime('waktu');
            $table->string('lokasi', 150);
            // Catatan untuk pelapor (misalnya dokumen yang perlu dibawa), disimpan terenkripsi.
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'dibatalkan'])->default('menunggu');
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->timestamps();

            $table->index(['report_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
