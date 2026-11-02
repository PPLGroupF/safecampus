<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // Kosong untuk aksi pelapor anonim atau percobaan login gagal.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('peran', ['mahasiswa', 'petugas', 'admin'])->nullable();
            $table->string('aksi', 100);
            $table->string('modul', 50);
            $table->string('kode_tiket', 50)->nullable();
            $table->enum('status', ['berhasil', 'gagal'])->default('berhasil');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['modul', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
