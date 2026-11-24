<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat penugasan laporan ke petugas. Petugas yang sedang bertanggung jawab
        // juga disimpan di reports.petugas_id agar mudah dipakai untuk filter dan hak akses.
        Schema::create('report_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('ditugaskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            // Alasan wajib diisi saat penugasan dialihkan ke petugas lain.
            $table->string('alasan', 255)->nullable();
            $table->boolean('aktif')->default(true);
            // Terisi jika penugasan dicabut (laporan kembali ke antrean bersama tanpa penanggung jawab).
            $table->timestamp('dicabut_at')->nullable();
            $table->foreignId('dicabut_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['report_id', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_assignments');
    }
};
