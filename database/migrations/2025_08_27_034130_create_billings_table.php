<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_billing', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg', 20);
            $table->decimal('total_obat', 12, 2);
            $table->decimal('total_jasa', 12, 2);
            $table->decimal('total_tagihan', 12, 2);
            $table->enum('status_pembayaran', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->date('tgl_billing');
            $table->timestamps();

            // Foreign key
            $table->foreign('no_reg')->references('no_reg')->on('tbl_daftar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_billing');
    }
};