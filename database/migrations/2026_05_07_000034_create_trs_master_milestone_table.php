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
        Schema::create('trs_master_milestone', function (Blueprint $table) {
            $table->id();
            $table->integer('initiative_id');
            $table->string('startYear');
            $table->string('startQ', 2);
            $table->string('endYear');
            $table->string('endQ', 2);
            $table->text('acitvity');
            $table->string('version', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_master_milestone');
    }
};