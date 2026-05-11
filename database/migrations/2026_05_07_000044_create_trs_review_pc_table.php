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
        Schema::create('trs_review_pc', function (Blueprint $table) {
            $table->id();
            $table->integer('initiative_id');
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('kesimpulan', 255);
            $table->text('detail_kesimpulan');
            $table->text('penjelasan');
            $table->text('why');
            $table->string('what', 255);
            $table->text('how');
            $table->text('project_profile');
            $table->text('key_milestone');
            $table->text('risk_impact');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_review_pc');
    }
};