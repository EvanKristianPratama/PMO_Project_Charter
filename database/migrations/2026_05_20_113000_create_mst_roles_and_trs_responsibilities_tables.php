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
        Schema::create('mst_roles', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->string('name', 255);
            $blueprint->text('description')->nullable();
            $blueprint->timestamps();
        });

        Schema::create('trs_responsibilities', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('role_id');
            $blueprint->text('content');
            $blueprint->timestamps();

            $blueprint->foreign('role_id')
                ->references('id')
                ->on('mst_roles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_responsibilities');
        Schema::dropIfExists('mst_roles');
    }
};
