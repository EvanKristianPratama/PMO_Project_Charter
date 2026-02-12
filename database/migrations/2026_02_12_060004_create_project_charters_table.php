<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trs_project_charters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('trs_projects')->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('duration')->nullable();
            $table->text('background')->nullable();
            $table->text('objectives')->nullable();
            $table->text('scope')->nullable();
            $table->text('impact_value')->nullable();
            $table->text('key_personnel')->nullable();
            $table->text('key_items')->nullable();
            $table->string('budget')->nullable();
            $table->text('risks_identified')->nullable();
            $table->text('risk_mitigation')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trs_project_charters');
    }
};
