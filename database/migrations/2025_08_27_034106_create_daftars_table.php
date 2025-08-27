<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_daftar', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg', 20)->unique();
            $table->string('no_mr', 20);
            $table->string('kode_poli', 10);
            $table->string('nama_poli', 100);
            $table->date('tgl_kunjungan');
            $table->unsignedBigInteger('dokter_id');
            $table->enum('cara_bayar', ['jkn', 'non jkn']);
            $table->timestamps();

            // Foreign keys
            $table->foreign('no_mr')->references('no_mr')->on('tbl_pasien')->onDelete('cascade');
            $table->foreign('dokter_id')->references('id')->on('tbl_dokter')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_daftar');
    }
};