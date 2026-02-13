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
        Schema::table('mst_goals', function (Blueprint $table) {
            // Add soft deletes if not exists
            if (!Schema::hasColumn('mst_goals', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_goals', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
