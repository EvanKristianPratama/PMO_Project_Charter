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
        Schema::create('mst_initiative_relation', function (Blueprint $table) {
            $table->id();
            $table->string('model_relasi', 50);
            $table->integer('initiative_code_row')->nullable();
            $table->integer('initiative_code_column')->nullable();
            $table->integer('type_relation');
            $table->string('justifikasi', 255)->nullable()->default('TBC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_initiative_relation');
    }
};