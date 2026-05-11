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
        Schema::create('trs_business_strategy', function (Blueprint $table) {
            $table->id();
            $table->integer('business_unit');
            $table->string('maximazing_value', 500)->nullable();
            $table->string('expand', 500)->nullable();
            $table->string('low_carbon', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_business_strategy');
    }
};