<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add nullable first to avoid unique constraint issues on existing rows
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
        });

        // Backfill usernames for existing users
        \Illuminate\Support\Facades\DB::table('users')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $base = $user->username ?? null;
                if (!$base) {
                    // Prefer email local part, fallback to sanitized name, then user{id}
                    $local = null;
                    if (!empty($user->email)) {
                        $local = explode('@', $user->email)[0];
                    }
                    $candidate = $local ?: preg_replace('/[^A-Za-z0-9_\-]/', '', strtolower($user->name ?? ''));
                    if (!$candidate) {
                        $candidate = 'user'.$user->id;
                    }

                    // Ensure uniqueness by appending suffix if needed
                    $final = $candidate;
                    $suffix = 1;
                    while (\Illuminate\Support\Facades\DB::table('users')->where('username', $final)->exists()) {
                        $final = $candidate.'_'.$suffix;
                        $suffix++;
                    }

                    \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['username' => $final]);
                }
            }
        });

        // Make column not nullable and unique with index
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable(false)->change();
                // Unique index may already exist from previous attempts; skip adding here
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop both unique and index if exist, then drop column
            try { $table->dropUnique('users_username_unique'); } catch (\Throwable $e) {}
            $table->dropColumn('username');
        });
    }
};
