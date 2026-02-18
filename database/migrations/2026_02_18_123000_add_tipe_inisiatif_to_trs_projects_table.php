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
        if (!Schema::hasTable('trs_projects')) {
            return;
        }

        if (Schema::hasColumn('trs_projects', 'tipe_inisiative')) {
            return;
        }

        Schema::table('trs_projects', function (Blueprint $table): void {
            $table->string('tipe_inisiative')->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('trs_projects')) {
            return;
        }

        if (!Schema::hasColumn('trs_projects', 'tipe_inisiative')) {
            return;
        }

        Schema::table('trs_projects', function (Blueprint $table): void {
            $table->dropColumn('tipe_inisiative');
        });
    }
};
