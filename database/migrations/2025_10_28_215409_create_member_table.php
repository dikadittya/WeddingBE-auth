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
        Schema::create('member', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan')->nullable(true)->default(null);
            $table->text('alamat')->nullable(true)->default(null);
            $table->string('nomor_hp', 15)->nullable(true)->default(null);
            $table->tinyInteger('is_core_team')->default(0);
            $table->string('key_name')->comment("id Name Personalia")->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member');
    }
};
