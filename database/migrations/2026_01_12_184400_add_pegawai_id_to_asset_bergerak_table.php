<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_bergerak', function (Blueprint $table) {
            $table->foreignId('pegawai_id')
                ->nullable()
                ->after('gedung_id')
                ->constrained('pegawai')
                ->nullOnDelete();

            $table->index('pegawai_id');
        });
    }

    public function down(): void
    {
        Schema::table('asset_bergerak', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });
    }
};
