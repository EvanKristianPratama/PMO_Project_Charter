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
        // Ubah posisi kolom status di trs_projects
        Schema::table('trs_projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->after('owner_name')->change();
        });

        // Ubah posisi kolom status di mst_digitalInitiative
        Schema::table('mst_digitalInitiative', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->after('coe')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_projects', function (Blueprint $table) {
            //
        });
    }
};
