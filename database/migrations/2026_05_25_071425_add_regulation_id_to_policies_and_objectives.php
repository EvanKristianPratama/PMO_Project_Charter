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
            $table->unsignedBigInteger('regulation_id')->nullable()->after('id');
            $table->foreign('regulation_id')->references('id')->on('mst_regulation')->onDelete('cascade');
        });

        Schema::table('mst_objective', function (Blueprint $table) {
            $table->unsignedBigInteger('regulation_id')->nullable()->after('objective_id');
            $table->foreign('regulation_id')->references('id')->on('mst_regulation')->onDelete('cascade');
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
