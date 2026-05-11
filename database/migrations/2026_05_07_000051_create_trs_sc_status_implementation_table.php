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
        Schema::create('trs_sc_status_implementation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('digital_initiative_id');
            $table->string('status', 255)->nullable();
            $table->date('date')->nullable();
            $table->time('time_start')->nullable();
            $table->string('review_status', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_sc_status_implementation');
    }
};