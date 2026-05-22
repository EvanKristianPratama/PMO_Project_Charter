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
        Schema::table('mst_actor', function (Blueprint $table) {
            if (!Schema::hasColumn('mst_actor', 'organization_id')) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_actor', function (Blueprint $table) {
            if (Schema::hasColumn('mst_actor', 'organization_id')) {
                $table->dropColumn('organization_id');
            }
        });
    }
};
