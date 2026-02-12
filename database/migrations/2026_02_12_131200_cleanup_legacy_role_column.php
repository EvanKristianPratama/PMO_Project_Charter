<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        if (Schema::hasColumn('users', 'app_role')) {
            DB::statement("
                UPDATE users
                SET app_role = CASE
                    WHEN LOWER(TRIM(role)) = 'admin' THEN 'admin'
                    WHEN LOWER(TRIM(role)) = 'user' THEN 'user'
                    ELSE app_role
                END
            ");
        }

        try {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['role']);
            });
        } catch (\Throwable) {
            // Legacy index may not exist.
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role')->nullable()->after('app_role');
                $table->index('role');
            });
        }
    }
};
