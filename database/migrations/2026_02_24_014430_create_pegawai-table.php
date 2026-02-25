

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama')->nullable();
            $table->unsignedTinyInteger('ue1')->comment('Unit Eselon 1: 1-14');
            $table->enum('kategori_kinerja', ['Di atas ekspektasi', 'Sesuai ekspektasi', 'Di bawah ekspektasi']);
            $table->enum('kategori_potensial', ['potensial rendah', 'potensial menengah', 'potensial tinggi']);
            $table->unsignedTinyInteger('box')->comment('Box 1-9 based on kinerja & potensial');
            $table->timestamps();

            $table->index('ue1');
            $table->index('box');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};

// 
