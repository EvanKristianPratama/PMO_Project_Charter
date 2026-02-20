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
        Schema::create('trs_digital_group', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('digital_id');
            $table->unsignedInteger('groub_id');
            $table->timestamps();
            
            $table->foreign('digital_id')
                ->references('id')
                ->on('trs_digital_initiative')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('groub_id')
                ->references('id')
                ->on('trs_organization')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_digital_group');
    }
};
