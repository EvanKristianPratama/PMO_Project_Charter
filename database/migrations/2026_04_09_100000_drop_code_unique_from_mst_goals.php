<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mst_goals', function (Blueprint $table): void {
            $table->dropUnique('mst_goals_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('mst_goals', function (Blueprint $table): void {
            $table->unique('code', 'mst_goals_code_unique');
        });
    }
};
