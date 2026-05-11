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
        Schema::create('trs_review_pc_status_implementation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->string('review_status', 20)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_review_pc_status_implementation');
    }
};