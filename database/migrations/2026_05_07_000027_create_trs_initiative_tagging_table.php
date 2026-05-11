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
        Schema::create('trs_initiative_tagging', function (Blueprint $table) {
            $table->id();
            $table->integer('initiative_id');
            $table->string('goal', 255)->nullable();
            $table->string('pilar', 45)->nullable();
            $table->bigInteger('themes_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_initiative_tagging');
    }
};