<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->normalizeColumn('mst_digitalInitiative', 'tipe_inisiative', [
            'tipe_initiative',
            'tipe_inisiatif',
        ]);

        $this->normalizeColumn('trs_projects', 'tipe_inisiative', [
            'tipe_initiative',
            'tipe_inisiatif',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep as no-op to avoid destructive rollback on production data.
    }

    private function normalizeColumn(string $tableName, string $canonicalColumn, array $aliasColumns): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (!Schema::hasColumn($tableName, $canonicalColumn)) {
            Schema::table($tableName, function (Blueprint $table) use ($canonicalColumn): void {
                $table->string($canonicalColumn)->nullable();
            });
        }

        foreach ($aliasColumns as $aliasColumn) {
            if ($aliasColumn === $canonicalColumn || !Schema::hasColumn($tableName, $aliasColumn)) {
                continue;
            }

            DB::table($tableName)
                ->where(function ($query) use ($canonicalColumn): void {
                    $query->whereNull($canonicalColumn)->orWhere($canonicalColumn, '');
                })
                ->whereNotNull($aliasColumn)
                ->where($aliasColumn, '!=', '')
                ->update([$canonicalColumn => DB::raw($aliasColumn)]);

            Schema::table($tableName, function (Blueprint $table) use ($aliasColumn): void {
                $table->dropColumn($aliasColumn);
            });
        }
    }
};

