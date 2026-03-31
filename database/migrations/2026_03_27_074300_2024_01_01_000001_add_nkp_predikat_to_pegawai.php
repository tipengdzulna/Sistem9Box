<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // NKP: nilai kinerja pegawai, desimal, nullable
            $table->decimal('nkp', 6, 2)->nullable()->after('nama');
            // Predikat kinerja: dropdown 5 nilai
            $table->string('predikat_kinerja', 30)->nullable()->after('nkp');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['nkp', 'predikat_kinerja']);
        });
    }
};