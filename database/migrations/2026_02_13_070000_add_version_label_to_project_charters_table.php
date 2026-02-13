<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trs_project_charters', function (Blueprint $table): void {
            $table->string('version_label')->nullable()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('trs_project_charters', function (Blueprint $table): void {
            $table->dropColumn('version_label');
        });
    }
};
