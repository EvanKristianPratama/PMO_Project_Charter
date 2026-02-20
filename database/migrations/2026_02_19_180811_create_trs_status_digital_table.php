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
        Schema::create('trs_status_digital', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('phase_id');
            $table->string('name', 50);
            $table->timestamps();

            $table->foreign('phase_id')
                ->references('id')
                ->on('mst_phase_digital')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_status_digital');
    }
};
