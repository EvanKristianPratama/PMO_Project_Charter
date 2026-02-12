<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'app_role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('app_role')->default('user')->after('status');
                $table->index('app_role');
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            DB::statement("
                UPDATE users
                SET app_role = CASE
                    WHEN LOWER(TRIM(role)) = 'admin' THEN 'admin'
                    WHEN LOWER(TRIM(role)) = 'user' THEN 'user'
                    ELSE app_role
                END
            ");
        }

        DB::statement("
            UPDATE users
            SET app_role = CASE
                WHEN LOWER(TRIM(app_role)) = 'admin' THEN 'admin'
                ELSE 'user'
            END
        ");

        if (Schema::hasColumn('users', 'role')) {
            try {
                Schema::table('users', function (Blueprint $table): void {
                    $table->dropIndex(['role']);
                });
            } catch (\Throwable) {
                // Role index may not exist in all environments.
            }

            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'app_role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['app_role']);
                $table->dropColumn('app_role');
            });
        }
    }
};
