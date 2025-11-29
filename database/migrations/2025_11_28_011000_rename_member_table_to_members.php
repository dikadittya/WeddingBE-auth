<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('member') && !Schema::hasTable('members')) {
            Schema::rename('member', 'members');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('members') && !Schema::hasTable('member')) {
            Schema::rename('members', 'member');
        }
    }
};
