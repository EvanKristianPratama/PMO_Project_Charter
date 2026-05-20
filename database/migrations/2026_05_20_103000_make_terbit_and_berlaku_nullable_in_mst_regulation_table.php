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
        Schema::table('mst_regulation', function (Blueprint $table) {
            $table->date('terbit')->nullable()->change();
            $table->date('berlaku')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_regulation', function (Blueprint $table) {
            $table->date('terbit')->nullable(false)->change();
            $table->date('berlaku')->nullable(false)->change();
        });
    }
};
