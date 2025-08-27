<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_resep', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg', 20);
            $table->string('kode_obat', 10);
            $table->integer('jumlah');
            $table->timestamps();

            // Foreign keys
            $table->foreign('no_reg')->references('no_reg')->on('tbl_daftar')->onDelete('cascade');
            $table->foreign('kode_obat')->references('kode_obat')->on('tbl_obat')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_resep');
    }
};