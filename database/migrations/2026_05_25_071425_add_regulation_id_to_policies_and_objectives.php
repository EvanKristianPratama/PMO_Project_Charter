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
        Schema::table('mst_general_policy', function (Blueprint $table) {
            if (!Schema::hasColumn('mst_general_policy', 'regulation_id')) {
                $table->unsignedInteger('regulation_id')->nullable()->after('id');
            }
        });

        Schema::table('mst_objective', function (Blueprint $table) {
            if (!Schema::hasColumn('mst_objective', 'regulation_id')) {
                $table->unsignedInteger('regulation_id')->nullable()->after('objective_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_general_policy', function (Blueprint $table) {
            $table->dropForeign(['regulation_id']);
            $table->dropColumn('regulation_id');
        });

        Schema::table('mst_objective', function (Blueprint $table) {
            $table->dropForeign(['regulation_id']);
            $table->dropColumn('regulation_id');
        });
    }
};
