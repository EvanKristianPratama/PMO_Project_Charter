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
        if (!Schema::hasTable('trs_responsible_objective')) {
            Schema::create('trs_responsible_objective', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('responsible_id');
                $table->string('objective_id', 255);
                $table->timestamps();

                $table->foreign('responsible_id')
                    ->references('id')
                    ->on('mst_responsible')
                    ->onDelete('cascade');

                $table->foreign('objective_id')
                    ->references('objective_id')
                    ->on('mst_objective')
                    ->onDelete('cascade');

                // Make the combination of responsible and objective unique
                $table->unique(['responsible_id', 'objective_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_responsible_objective');
    }
};
