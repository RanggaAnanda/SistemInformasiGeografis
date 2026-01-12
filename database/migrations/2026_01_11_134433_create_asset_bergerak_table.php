<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_bergerak', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('jenis');
            $table->foreignId('gedung_id')->constrained('gedung')->cascadeOnDelete();
            $table->foreignId('kategori_asset_id')->constrained('kategori_asset')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'dipindahkan', 'rusak'])->default('aktif');
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_bergerak');
    }
};
