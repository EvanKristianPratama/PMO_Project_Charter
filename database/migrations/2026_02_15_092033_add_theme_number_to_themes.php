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
        Schema::table('trs_themes', function (Blueprint $table) {
            $table->integer('theme_number')->after('idGoal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_themes', function (Blueprint $table) {
            $table->dropColumn('theme_number');
        });
    }
};
