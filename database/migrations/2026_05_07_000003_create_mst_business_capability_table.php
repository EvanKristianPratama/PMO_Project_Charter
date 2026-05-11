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
        Schema::create('mst_business_capability', function (Blueprint $table) {
            $table->id();
            $table->string('group_business', 255)->nullable();
            $table->string('group_function', 255)->nullable();
            $table->string('subGroup_function', 255)->nullable();
            $table->string('subSubGroup_function', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_business_capability');
    }
};