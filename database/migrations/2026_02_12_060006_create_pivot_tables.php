<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trs_program_project', function (Blueprint $table) {
            $table->foreignId('program_id')->constrained('mst_programs')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('trs_projects')->cascadeOnDelete();
            $table->primary(['program_id', 'project_id']);
        });

        Schema::create('trs_goal_project', function (Blueprint $table) {
            $table->foreignId('goal_id')->constrained('mst_goals')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('trs_projects')->cascadeOnDelete();
            $table->primary(['goal_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trs_goal_project');
        Schema::dropIfExists('trs_program_project');
    }
};
