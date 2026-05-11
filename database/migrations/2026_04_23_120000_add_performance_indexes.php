<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function down(): void
    {
        Schema::table('mst_initiative', function (Blueprint $table) {
            $table->dropIndex(['tipe_initiative']);
            $table->dropIndex(['business_unit']);
            $table->dropIndex(['source']);
            $table->dropIndex(['coe_id']);
        });

        Schema::table('initiative_tagging', function (Blueprint $table) {
            $table->dropIndex(['initiative_id']);
            $table->dropIndex(['theme_id']);
        });

        Schema::table('trs_initiative_support', function (Blueprint $table) {
            $table->dropIndex(['digital_initiative_id']);
            $table->dropIndex(['it_initiative_id']);
        });

        Schema::table('trs_map_it_building', function (Blueprint $table) {
            $table->dropIndex(['primary']);
            $table->dropIndex(['secondary']);
            $table->dropIndex(['initiative_id']);
        });
    }

    public function up(): void
    {
        Schema::table('mst_initiative', function (Blueprint $table) {
            $table->index('tipe_initiative');
            $table->index('business_unit');
            $table->index('source');
            $table->index('coe_id');
        });

        Schema::table('initiative_tagging', function (Blueprint $table) {
            $table->index('initiative_id');
            $table->index('theme_id');
        });

        Schema::table('trs_initiative_support', function (Blueprint $table) {
            $table->index('digital_initiative_id');
            $table->index('it_initiative_id');
        });

        Schema::table('trs_map_it_building', function (Blueprint $table) {
            $table->index('primary');
            $table->index('secondary');
            $table->index('initiative_id');
        });
    }
};
