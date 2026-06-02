<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backup data lama
        $oldData = DB::table('trs_map_actor_sop')->get()->map(function ($row) {
            return (array) $row;
        })->toArray();

        // Recreate table secara aman
        Schema::dropIfExists('trs_map_actor_sop');
        Schema::create('trs_map_actor_sop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('sop_id');
            $table->string('tipe', 255)->nullable();
            $table->timestamps();
        });

        // Masukkan kembali data lama dengan tipe yang dihitung
        foreach ($oldData as $row) {
            $tipe = $row['tipe'] ?? DB::table('mst_sop')->where('id', $row['sop_id'])->value('tipe');
            DB::table('trs_map_actor_sop')->insert([
                'actor_id' => $row['actor_id'],
                'sop_id' => $row['sop_id'],
                'tipe' => $tipe ?: 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
