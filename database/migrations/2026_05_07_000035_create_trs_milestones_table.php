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
        Schema::create('trs_milestones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pc_id')->nullable();
            $table->string('title', 255);
            $table->text('output')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('type', 255)->default('milestone');
            $table->tinyInteger('milestone_type')->default(1);
            $table->integer('order')->default(0);
            $table->string('version', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_milestones');
    }
};