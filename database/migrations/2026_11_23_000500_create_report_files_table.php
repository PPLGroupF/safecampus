<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bukti pendukung laporan. File disimpan di penyimpanan privat (bukan folder public).
        Schema::create('report_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            // Nama asli file bisa memuat nama orang, jadi disimpan terenkripsi.
            $table->text('nama_file');
            // Lokasi file di disk "bukti" dengan nama acak.
            $table->string('path', 255);
            $table->string('mime', 100);
            $table->unsignedInteger('ukuran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_files');
    }
};
