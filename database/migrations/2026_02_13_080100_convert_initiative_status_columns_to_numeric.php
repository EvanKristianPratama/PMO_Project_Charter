<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->convertToNumericStatus('trs_projects');
        $this->convertToNumericStatus('mst_digitalInitiative');
    }

    public function down(): void
    {
        $this->convertToStringStatus('trs_projects');
        $this->convertToStringStatus('mst_digitalInitiative');
    }

    private function convertToNumericStatus(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->unsignedTinyInteger('status_id')->default(1);
        });

        DB::table($tableName)->update([
            'status_id' => DB::raw("CASE LOWER(COALESCE(status, ''))
                WHEN 'propose' THEN 1
                WHEN 'draft' THEN 1
                WHEN 'review' THEN 2
                WHEN 'on_hold' THEN 2
                WHEN 'onhold' THEN 2
                WHEN 'on_progress' THEN 2
                WHEN 'onprogress' THEN 2
                WHEN 'in_progress' THEN 2
                WHEN 'inprogress' THEN 2
                WHEN 'approve' THEN 3
                WHEN 'approved' THEN 3
                WHEN 'active' THEN 3
                WHEN 'baseline' THEN 4
                WHEN 'completed' THEN 4
                WHEN 'complete' THEN 4
                ELSE 1
            END"),
        ]);

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->renameColumn('status_id', 'status');
        });
    }

    private function convertToStringStatus(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('status_name')->default('propose');
        });

        DB::table($tableName)->update([
            'status_name' => DB::raw("CASE status
                WHEN 1 THEN 'propose'
                WHEN 2 THEN 'review'
                WHEN 3 THEN 'approve'
                WHEN 4 THEN 'baseline'
                ELSE 'propose'
            END"),
        ]);

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->renameColumn('status_name', 'status');
        });
    }
};
