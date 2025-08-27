<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat', 10)->unique();
            $table->string('nama_obat', 100);
            $table->integer('stok');
            $table->decimal('harga', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_obat');
    }
};