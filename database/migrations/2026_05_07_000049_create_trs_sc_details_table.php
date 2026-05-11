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
        Schema::create('trs_sc_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sc_id');
            $table->string('organization', 255)->nullable();
            $table->text('situation')->nullable();
            $table->text('key_functionalities')->nullable();
            $table->text('value_rationale')->nullable();
            $table->text('value_matrics')->nullable();
            $table->text('urgency_rationale')->nullable();
            $table->text('urgency_expected')->nullable();
            $table->string('expected_q', 2)->nullable();
            $table->string('year_q')->nullable();
            $table->integer('ease')->nullable();
            $table->text('ease_rationale')->nullable();
            $table->text('ease_detail')->nullable();
            $table->integer('resource')->nullable();
            $table->text('resource_rationale')->nullable();
            $table->text('resource_detail')->nullable();
            $table->string('predecessor', 255)->nullable();
            $table->string('successor', 255)->nullable();
            $table->string('otherBU', 255)->nullable();
            $table->json('sign_by')->nullable();
            $table->date('update_doc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_sc_details');
    }
};