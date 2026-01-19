<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_asset', function (Blueprint $table) {
            $table->id();

            // Asset yang dimutasi
            $table->foreignId('asset_bergerak_id')
                ->constrained('asset_bergerak')
                ->cascadeOnDelete();

            // Lokasi asal & tujuan
            $table->foreignId('from_gedung_id')
                ->nullable()
                ->constrained('gedung')
                ->nullOnDelete();

            $table->foreignId('to_gedung_id')
                ->nullable()
                ->constrained('gedung')
                ->nullOnDelete();

            // Penanggung jawab asal & tujuan
            $table->foreignId('from_pegawai_id')
                ->nullable()
                ->constrained('pegawai')
                ->nullOnDelete();

            $table->foreignId('to_pegawai_id')
                ->nullable()
                ->constrained('pegawai')
                ->nullOnDelete();

            // Status mutasi
            $table->enum('status', [
                'draft',
                'approved',
                'rejected'
            ])->default('draft');

            // Jenis mutasi (INI WAJIB)
            $table->enum('jenis_mutasi', [
                'klaim',
                'pengembalian',
                'internal',
                'antar_gedung',
            ]);

            $table->text('catatan')->nullable();

            // Approval
            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            // Keterangan
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_asset');
    }
};
