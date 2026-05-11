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
        Schema::create('trs_status_mstinitiative', function (Blueprint $table) {
            $table->id();
            $table->integer('initiative_id');
            $table->string('status', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_status_mstinitiative');
    }
};