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
        Schema::create('trs_project_status_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_charter_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('version');
            $table->date('tanggal')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_project_status_history');
    }
};