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
        Schema::create('mst_objective', function (Blueprint $blueprint) {
            $blueprint->string('objective_id', 255)->primary();
            $blueprint->string('domain', 255)->nullable();
            $blueprint->string('objective', 255);
            $blueprint->text('objective_description')->nullable();
            $blueprint->text('objective_purpose')->nullable();
        });

        Schema::create('mst_practice', function (Blueprint $blueprint) {
            $blueprint->string('practice_id', 255)->primary();
            $blueprint->string('objective_id', 255);
            $blueprint->string('practice_name', 255)->nullable();
            $blueprint->text('practice_description')->nullable();

            $blueprint->foreign('objective_id')
                ->references('objective_id')
                ->on('mst_objective')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_practice');
        Schema::dropIfExists('mst_objective');
    }
};
