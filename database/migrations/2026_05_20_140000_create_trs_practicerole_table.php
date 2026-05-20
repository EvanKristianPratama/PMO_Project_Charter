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
        if (!Schema::hasTable('trs_practicerole')) {
            Schema::create('trs_practicerole', function (Blueprint $table) {
                $table->id();
                $table->string('practice_id', 255);
                $table->unsignedBigInteger('role_id');
                $table->string('r_a', 10)->nullable(); // Stores R, A, C, I or null
                $table->timestamps();

                $table->foreign('practice_id')
                    ->references('practice_id')
                    ->on('mst_practice')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('mst_roles')
                    ->onDelete('cascade');

                // Make the combination of practice and role unique
                $table->unique(['practice_id', 'role_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_practicerole');
    }
};
