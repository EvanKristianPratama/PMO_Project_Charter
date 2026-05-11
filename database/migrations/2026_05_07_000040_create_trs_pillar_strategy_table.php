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
        Schema::create('trs_pillar_strategy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('themes_id');
            $table->string('title', 100)->nullable();
            $table->string('strategy', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_pillar_strategy');
    }
};