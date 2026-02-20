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
        Schema::create('trs_rjpp', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('digital_id');
            $table->unsignedBigInteger('theme_id');
            $table->timestamps();

            $table->foreign('digital_id')
                ->references('id')
                ->on('trs_digital_initiative')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('theme_id')
                ->references('id')
                ->on('trs_themes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_rjpp');
    }
};

