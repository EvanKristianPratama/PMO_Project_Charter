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
        Schema::create('trs_pc_version_analysis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->string('version_label', 255)->nullable();
            $table->string('sponsor', 255)->nullable();
            $table->string('owner', 255)->nullable();
            $table->string('leader', 255)->nullable();
            $table->string('tgl_dokumen', 255)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('duration', 255)->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
            $table->text('background')->nullable();
            $table->text('objectives')->nullable();
            $table->text('target_kpi')->nullable();
            $table->text('impact_value')->nullable();
            $table->text('key_personnel')->nullable();
            $table->text('key_items')->nullable();
            $table->string('budget', 255)->nullable();
            $table->text('risks_identified')->nullable();
            $table->text('risk_mitigation')->nullable();
            $table->text('key_milestone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_pc_version_analysis');
    }
};