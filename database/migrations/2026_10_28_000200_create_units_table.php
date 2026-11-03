<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('campuses')->restrictOnDelete();
            $table->string('nama_unit', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['campus_id', 'nama_unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
