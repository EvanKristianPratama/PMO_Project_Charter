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
        Schema::create('trs_sop_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regulation_id')->nullable();
            $table->string('tipe');
            $table->timestamps();

            $table->foreign('regulation_id')->references('id')->on('mst_regulation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_sop_category');
    }
};
