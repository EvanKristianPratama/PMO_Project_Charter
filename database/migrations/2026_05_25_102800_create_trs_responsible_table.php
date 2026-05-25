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
        if (!Schema::hasTable('trs_responsible')) {
            Schema::create('trs_responsible', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('responsible_id');
                $table->timestamps();

                $table->foreign('role_id')
                    ->references('id')
                    ->on('mst_roles')
                    ->onDelete('cascade');

                $table->foreign('responsible_id')
                    ->references('id')
                    ->on('mst_responsible')
                    ->onDelete('cascade');

                // Make the combination of role and responsible unique
                $table->unique(['role_id', 'responsible_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_responsible');
    }
};
