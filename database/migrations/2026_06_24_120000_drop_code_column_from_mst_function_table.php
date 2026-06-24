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
        Schema::table('mst_function', function (Blueprint $table) {
            if (Schema::hasColumn('mst_function', 'code')) {
                $table->dropColumn('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_function', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }
};
