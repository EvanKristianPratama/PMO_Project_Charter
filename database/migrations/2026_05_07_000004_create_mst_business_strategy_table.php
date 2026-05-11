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
        Schema::create('mst_business_strategy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('goal_id');
            $table->integer('misi_id');
            $table->string('code', 2)->nullable();
            $table->string('strategy', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_business_strategy');
    }
};