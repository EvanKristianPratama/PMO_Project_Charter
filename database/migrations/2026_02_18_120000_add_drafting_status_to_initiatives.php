<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('trs_status_initiative')) {
            return;
        }

        $baselineId = (int) (DB::table('trs_status_initiative')
            ->where('name', 'baseline')
            ->value('id') ?? 0);

        $hasDrafting = DB::table('trs_status_initiative')
            ->where('name', 'drafting')
            ->exists();

        // Already migrated to 5 statuses with drafting at the start.
        if ($baselineId === 5 && $hasDrafting) {
            return;
        }

        DB::transaction(function (): void {
            if (Schema::hasTable('trs_projects')) {
                DB::statement('UPDATE trs_projects SET status = status + 1 WHERE status BETWEEN 1 AND 4');
            }

            if (Schema::hasTable('mst_digitalInitiative')) {
                DB::statement('UPDATE mst_digitalInitiative SET status = status + 1 WHERE status BETWEEN 1 AND 4');
            }

            DB::statement('UPDATE trs_status_initiative SET id = id + 10 WHERE id BETWEEN 1 AND 4');

            DB::statement("UPDATE trs_status_initiative SET id = 2, name = 'propose', updated_at = NOW() WHERE id = 11");
            DB::statement("UPDATE trs_status_initiative SET id = 3, name = 'review', updated_at = NOW() WHERE id = 12");
            DB::statement("UPDATE trs_status_initiative SET id = 4, name = 'approve', updated_at = NOW() WHERE id = 13");
            DB::statement("UPDATE trs_status_initiative SET id = 5, name = 'baseline', updated_at = NOW() WHERE id = 14");

            DB::table('trs_status_initiative')->upsert([
                ['id' => 1, 'name' => 'drafting', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'propose', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'review', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'name' => 'approve', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 5, 'name' => 'baseline', 'created_at' => now(), 'updated_at' => now()],
            ], ['id'], ['name', 'updated_at']);

            DB::table('trs_status_initiative')
                ->whereBetween('id', [11, 14])
                ->delete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('trs_status_initiative')) {
            return;
        }

        $baselineId = (int) (DB::table('trs_status_initiative')
            ->where('name', 'baseline')
            ->value('id') ?? 0);

        $hasDrafting = DB::table('trs_status_initiative')
            ->where('name', 'drafting')
            ->exists();

        // Already in old mapping.
        if ($baselineId === 4 && !$hasDrafting) {
            return;
        }

        DB::transaction(function (): void {
            if (Schema::hasTable('trs_projects')) {
                DB::statement('UPDATE trs_projects SET status = status - 1 WHERE status BETWEEN 2 AND 5');
            }

            if (Schema::hasTable('mst_digitalInitiative')) {
                DB::statement('UPDATE mst_digitalInitiative SET status = status - 1 WHERE status BETWEEN 2 AND 5');
            }

            DB::statement('UPDATE trs_status_initiative SET id = id + 10 WHERE id BETWEEN 2 AND 5');

            DB::statement("UPDATE trs_status_initiative SET id = 1, name = 'propose', updated_at = NOW() WHERE id = 12");
            DB::statement("UPDATE trs_status_initiative SET id = 2, name = 'review', updated_at = NOW() WHERE id = 13");
            DB::statement("UPDATE trs_status_initiative SET id = 3, name = 'approve', updated_at = NOW() WHERE id = 14");
            DB::statement("UPDATE trs_status_initiative SET id = 4, name = 'baseline', updated_at = NOW() WHERE id = 15");

            DB::table('trs_status_initiative')
                ->where('name', 'drafting')
                ->delete();

            DB::table('trs_status_initiative')
                ->whereBetween('id', [12, 15])
                ->delete();

            DB::table('trs_status_initiative')->upsert([
                ['id' => 1, 'name' => 'propose', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'review', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'approve', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'name' => 'baseline', 'created_at' => now(), 'updated_at' => now()],
            ], ['id'], ['name', 'updated_at']);
        });
    }
};
