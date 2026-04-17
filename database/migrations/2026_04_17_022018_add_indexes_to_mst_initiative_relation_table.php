<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mst_initiative_relation', function (Blueprint $table) {
            $table->index('initiative_code_row');
            $table->index('initiative_code_column');
            $table->index('model_relasi');
        });
    }

    public function down(): void
    {
        Schema::table('mst_initiative_relation', function (Blueprint $table) {
            $table->dropIndex(['initiative_code_row']);
            $table->dropIndex(['initiative_code_column']);
            $table->dropIndex(['model_relasi']);
        });
    }
};
