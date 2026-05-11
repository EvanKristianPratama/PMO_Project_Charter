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
        Schema::create('trs_sc_initiative', function (Blueprint $table) {
            $table->id();
            $table->string('owner', 255)->nullable();
            $table->string('coe', 255)->nullable();
            $table->string('usecase', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('source_id')->nullable();
            $table->integer('value')->nullable();
            $table->integer('urgency')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_sc_initiative');
    }
};