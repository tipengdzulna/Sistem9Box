<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {

            // ── Kompetensi (bobot 70%) ─────────────────────────────
            $table->decimal('kompetensi_teknis', 5, 2)->nullable()->after('box_kemenkeu')
                ->comment('Nilai kompetensi teknis, rentang 0–100');
            $table->decimal('kompetensi_mansoskul', 5, 2)->nullable()->after('kompetensi_teknis')
                ->comment('Nilai kompetensi manajerial & sosio-kultural, rentang 0–100');

            // ── Potensi 9 Komponen (bobot 20%) ────────────────────
            for ($i = 1; $i <= 9; $i++) {
                $table->decimal("potensi_{$i}", 5, 2)->nullable()->after('kompetensi_mansoskul')
                    ->comment("Nilai potensi komponen {$i}, rentang 0–100");
            }

            // ── Rekam Jejak (bobot 10%) ────────────────────────────
            $table->decimal('rj_pendidikan_formal', 5, 2)->nullable()
                ->comment('Rekam jejak: pendidikan formal, rentang 0–100');
            $table->decimal('rj_pelatihan', 5, 2)->nullable()
                ->comment('Rekam jejak: pelatihan, rentang 0–100');
            $table->decimal('rj_pengalaman', 5, 2)->nullable()
                ->comment('Rekam jejak: pengalaman, rentang 0–100');
            $table->decimal('rj_integritas', 5, 2)->nullable()
                ->comment('Rekam jejak: integritas, rentang 0–100');
            $table->decimal('rj_moralitas', 5, 2)->nullable()
                ->comment('Rekam jejak: moralitas, rentang 0–100');

            // ── Nilai Total Hasil Perhitungan ──────────────────────
            $table->decimal('nilai_potensi_total', 6, 2)->nullable()
                ->comment('Nilai potensi total hasil perhitungan (0–100): kompetensi 70% + potensi 20% + rekam jejak 10%');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'kompetensi_teknis',
                'kompetensi_mansoskul',
                'potensi_1', 'potensi_2', 'potensi_3',
                'potensi_4', 'potensi_5', 'potensi_6',
                'potensi_7', 'potensi_8', 'potensi_9',
                'rj_pendidikan_formal',
                'rj_pelatihan',
                'rj_pengalaman',
                'rj_integritas',
                'rj_moralitas',
                'nilai_potensi_total',
            ]);
        });
    }
};