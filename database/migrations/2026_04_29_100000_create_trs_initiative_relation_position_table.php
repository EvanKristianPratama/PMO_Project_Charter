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
        Schema::create('trs_initiative_relation_position', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('initiative_id')->unique();
            $table->float('x')->nullable();
            $table->float('y')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            // Only add foreign key if mst_initiative table exists (MySQL remote)
            // Skip on NativePHP SQLite where mst_initiative doesn't exist locally
            if (Schema::hasTable('mst_initiative')) {
                $table->foreign('initiative_id')->references('id')->on('mst_initiative')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_initiative_relation_position');
    }
};
