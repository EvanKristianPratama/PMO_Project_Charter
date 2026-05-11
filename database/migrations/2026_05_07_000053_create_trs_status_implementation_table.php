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
        Schema::create('trs_status_implementation', function (Blueprint $table) {
            $table->id();
            $table->integer('initiative_id')->nullable();
            $table->string('review_status', 11)->nullable();
            $table->string('pic', 255)->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('year')->nullable();
            $table->text('status_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_status_implementation');
    }
};