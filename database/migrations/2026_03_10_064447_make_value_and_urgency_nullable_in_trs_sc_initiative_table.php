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
        Schema::table('trs_sc_initiative', function (Blueprint $table) {
            $table->integer('value')->nullable()->change();
            $table->integer('urgency')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_sc_initiative', function (Blueprint $table) {
            $table->integer('value')->nullable(false)->default(4)->change();
            $table->integer('urgency')->nullable(false)->default(4)->change();
        });
    }
};
