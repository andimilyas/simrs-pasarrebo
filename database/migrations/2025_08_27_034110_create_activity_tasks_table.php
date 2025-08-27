<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_activity_task', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg', 20);
            $table->string('activity', 100);
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_selesai')->nullable();
            $table->string('petugas', 100);
            $table->timestamps();

            // Foreign key
            $table->foreign('no_reg')->references('no_reg')->on('tbl_daftar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_activity_task');
    }
};