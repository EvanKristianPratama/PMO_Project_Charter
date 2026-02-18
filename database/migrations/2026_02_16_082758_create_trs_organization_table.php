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
        Schema::create('trs_organization', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('groub_id');
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('groub_id')
                ->references('id')
                ->on('trs_groub')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_organization');
    }
};
