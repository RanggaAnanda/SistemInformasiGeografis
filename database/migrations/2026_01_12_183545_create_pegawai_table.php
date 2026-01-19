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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Relasi ke Gedung (home base pegawai)
            $table->foreignId('gedung_id')
                ->constrained('gedung')
                ->cascadeOnDelete();

            // Identitas pegawai
            $table->string('nip', 30)->nullable();
            $table->string('nama', 150);
            $table->string('jabatan', 100);

            // Status kepegawaian
            $table->string('status', 20)->default('aktif');
            // contoh nilai: aktif, nonaktif, mutasi

            // Catatan tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Index tambahan (opsional tapi recommended)
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
