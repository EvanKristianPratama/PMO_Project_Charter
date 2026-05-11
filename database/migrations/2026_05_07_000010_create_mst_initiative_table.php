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
        Schema::create('mst_initiative', function (Blueprint $table) {
            $table->id();
            $table->integer('coe_id')->nullable();
            $table->integer('tipe_initiative');
            $table->integer('business_unit')->nullable();
            $table->integer('code');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('status', 255)->nullable();
            $table->integer('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_initiative');
    }
};