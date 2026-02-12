<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'app_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('app_role')->default('user')->after('status');
                $table->index('app_role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'app_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['app_role']);
                $table->dropColumn('app_role');
            });
        }
    }
};
