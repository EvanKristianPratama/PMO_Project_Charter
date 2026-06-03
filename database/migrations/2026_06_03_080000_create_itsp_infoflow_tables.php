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
        if (!Schema::hasTable('trs_itsp_infoflow_inputs')) {
            Schema::create('trs_itsp_infoflow_inputs', function (Blueprint $table) {
                $table->id();
                $table->string('practice_id', 255);
                $table->string('from', 255);
                $table->text('description');
                $table->timestamps();

                $table->foreign('practice_id')
                    ->references('practice_id')
                    ->on('mst_practice')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('trs_itsp_infoflow_outputs')) {
            Schema::create('trs_itsp_infoflow_outputs', function (Blueprint $table) {
                $table->id();
                $table->string('practice_id', 255);
                $table->string('to', 255);
                $table->text('description');
                $table->timestamps();

                $table->foreign('practice_id')
                    ->references('practice_id')
                    ->on('mst_practice')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_itsp_infoflow_inputs');
        Schema::dropIfExists('trs_itsp_infoflow_outputs');
    }
};
