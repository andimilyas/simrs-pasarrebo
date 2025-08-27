<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_pasien', function (Blueprint $table) {
            $table->id();
            $table->string('no_mr', 20)->unique();
            $table->string('nama_pasien', 100);
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat', 200);
            $table->date('tgl_daftar');
            $table->string('no_bpjs', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_pasien');
    }
};