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
        Schema::dropIfExists('trs_proses_bisnis');
        Schema::create('trs_proses_bisnis', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->string('no');
            $table->text('proses_bisnis');
            $table->text('tugas');
            $table->text('hasil');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_proses_bisnis');
    }
};
