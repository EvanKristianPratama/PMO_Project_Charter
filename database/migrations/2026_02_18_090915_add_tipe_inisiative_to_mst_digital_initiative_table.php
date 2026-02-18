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
        if (!Schema::hasTable('mst_digitalInitiative')) {
            return;
        }

        if (Schema::hasColumn('mst_digitalInitiative', 'tipe_inisiative')) {
            return;
        }

        Schema::table('mst_digitalInitiative', function (Blueprint $table) {
            $table->string('tipe_inisiative')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('mst_digitalInitiative')) {
            return;
        }

        if (!Schema::hasColumn('mst_digitalInitiative', 'tipe_inisiative')) {
            return;
        }

        Schema::table('mst_digitalInitiative', function (Blueprint $table) {
            $table->dropColumn('tipe_inisiative');
        });
    }
};
