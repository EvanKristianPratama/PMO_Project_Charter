<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trs_map_actor_sop', function (Blueprint $table) {
            if (!Schema::hasColumn('trs_map_actor_sop', 'id')) {
                $table->id()->first();
            }

            if (!Schema::hasColumn('trs_map_actor_sop', 'tipe')) {
                $table->string('tipe', 1)->nullable()->after('sop_id');
            }

            if (!Schema::hasColumn('trs_map_actor_sop', 'created_at')) {
                $table->timestamps();
            }
        });

        if (Schema::hasColumn('trs_map_actor_sop', 'tipe')) {
            DB::table('trs_map_actor_sop')
                ->whereNull('trs_map_actor_sop.tipe')
                ->get(['id', 'sop_id'])
                ->each(function ($mapping) {
                    $tipe = DB::table('mst_sop')->where('id', $mapping->sop_id)->value('tipe');

                    if ($tipe) {
                        DB::table('trs_map_actor_sop')
                            ->where('id', $mapping->id)
                            ->update(['tipe' => $tipe]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('trs_map_actor_sop', function (Blueprint $table) {
            if (Schema::hasColumn('trs_map_actor_sop', 'created_at')) {
                $table->dropTimestamps();
            }

            if (Schema::hasColumn('trs_map_actor_sop', 'tipe')) {
                $table->dropColumn('tipe');
            }

            if (Schema::hasColumn('trs_map_actor_sop', 'id')) {
                $table->dropColumn('id');
            }
        });
    }
};
