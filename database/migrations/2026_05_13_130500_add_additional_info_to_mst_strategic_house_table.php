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
        if (Schema::hasTable('mst_strategic_house')) {
            Schema::table('mst_strategic_house', function (Blueprint $table) {
                if (!Schema::hasColumn('mst_strategic_house', 'additional_info')) {
                    $table->text('additional_info')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mst_strategic_house')) {
            Schema::table('mst_strategic_house', function (Blueprint $table) {
                if (Schema::hasColumn('mst_strategic_house', 'additional_info')) {
                    $table->dropColumn('additional_info');
                }
            });
        }
    }
};
