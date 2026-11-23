<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tanggapan pelapor pada laporannya, beserta balasan petugas (parent_id terisi).
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            // Kosong = tanggapan pelapor. Terisi = balasan petugas untuk tanggapan tersebut (SAF-450).
            $table->foreignId('parent_id')->nullable()->constrained('responses')->cascadeOnDelete();
            // Isi pesan disimpan terenkripsi (cast "encrypted" pada model).
            $table->text('pesan');
            // Waktu tanggapan dibaca petugas. Setelah dibaca, pelapor tidak bisa mengubah atau menghapusnya.
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->index(['report_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
