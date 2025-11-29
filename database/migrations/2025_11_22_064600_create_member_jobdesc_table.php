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
        Schema::create('member_jobdesc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_member')->constrained('member')->onDelete('cascade');
            $table->foreignId('id_jobdesc')->constrained('master_jobdesc')->onDelete('cascade');
            $table->string('nama_jobdesc')->comment("Kategori TW");
            $table->string('tipe_jobdesc')->nullable()->comment("Jenis TW");
            $table->string('key_job')->nullable()->comment("id TW");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_jobdesc');
    }
};
