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
        Schema::create('mst_priority_strategic_initiative', function (Blueprint $table) {
            $table->id();
            $table->string('priority', 255)->nullable();
            $table->integer('no')->nullable();
            $table->string('initiative', 255)->nullable();
            $table->string('deskripsi', 500)->nullable();
            $table->string('it_initiatives', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_priority_strategic_initiative');
    }
};