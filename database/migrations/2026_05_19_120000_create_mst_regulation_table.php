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
        Schema::create('mst_regulation', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('tipe');
            $table->string('owner');
            $table->string('revisi');
            $table->date('terbit')->nullable();
            $table->date('berlaku')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_regulation');
    }
};
