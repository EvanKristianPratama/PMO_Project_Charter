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
        Schema::create('trs_pc_status_implementation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->integer('target')->nullable();
            $table->integer('progress')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('status', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_pc_status_implementation');
    }
};