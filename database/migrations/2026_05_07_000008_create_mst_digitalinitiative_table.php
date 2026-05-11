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
        Schema::create('mst_digitalinitiative', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255)->nullable();
            $table->string('tipe_inisiative', 255)->nullable();
            $table->string('no', 255)->nullable();
            $table->string('projectOwner', 255)->nullable();
            $table->string('useCase', 255)->nullable();
            $table->text('desc')->nullable();
            $table->text('value')->nullable();
            $table->string('urgency', 255)->nullable();
            $table->string('rjjp', 255)->nullable();
            $table->string('coe', 255)->nullable();
            $table->tinyInteger('status');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_digitalinitiative');
    }
};