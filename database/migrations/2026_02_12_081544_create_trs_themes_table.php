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
        Schema::create('trs_themes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idGoal');
            $table->string('name');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('idGoal', 'fk_theme_goal')
                  ->references('id')
                  ->on('mst_goals')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            
            $table->index('idGoal', 'fk_theme_goal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_themes');
    }
};
