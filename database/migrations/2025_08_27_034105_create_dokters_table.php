<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_dokter', function (Blueprint $table) {
            $table->id();
            $table->string('kode_dokter', 10)->unique();
            $table->string('nama_dokter', 100);
            $table->string('spesialis', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_dokter');
    }
};