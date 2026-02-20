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
        Schema::create('trs_digital_initiative', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('useCase_id');
            $table->integer('value')->default(999)
                ->comment('999 = TBC (To be Discussed), 1 = Low, 2 = Medium, 3 = High');
            $table->integer('urgency')->default(999)
                ->comment('999 = TBC (To be Discussed), 1 = Low, 2 = Medium, 3 = High');
            $table->unsignedBigInteger('rjpp_tagging')->default(999)
                ->comment('999 = TBC (To be Discussed), 1 = Low, 2 = Medium, 3 = High');
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('trs_organization')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('useCase_id')
                ->references('id')
                ->on('trs_use_case')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('rjpp_tagging')
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
        Schema::dropIfExists('trs_digital_initiative');
    }
};