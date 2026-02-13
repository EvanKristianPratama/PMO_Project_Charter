<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_digitalInitiative', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('no')->nullable();
            $table->string('projectOwner')->nullable();
            $table->string('useCase')->nullable();
            $table->text('desc')->nullable();
            $table->text('value')->nullable();
            $table->string('urgency')->nullable();
            $table->string('rjjp')->nullable();
            $table->string('coe')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_digitalInitiative');
    }
};
