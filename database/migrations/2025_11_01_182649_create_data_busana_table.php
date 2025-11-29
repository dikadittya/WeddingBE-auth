<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_busana', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipe_busana');
            $table->string('tipe_busana', 255);
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('nama_busana', 255);
            $table->decimal('harga_beli', 15, 2);
            $table->date('tanggal_beli');
            $table->string('produk_by', 255);
            $table->timestamps();

            // Foreign key constraint to master_tipe_busana
            $table->foreign('id_tipe_busana')->references('id')->on('master_tipe_busana')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_busana');
    }
};
