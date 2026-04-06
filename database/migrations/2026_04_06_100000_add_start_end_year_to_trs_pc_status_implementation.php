<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trs_pc_status_implementation', function (Blueprint $table) {
            $table->enum('start', [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            ])->nullable()->after('date');

            $table->enum('end', [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            ])->nullable()->after('start');

            $table->string('year', 4)->nullable()->after('end');
        });
    }

    public function down(): void
    {
        Schema::table('trs_pc_status_implementation', function (Blueprint $table) {
            $table->dropColumn(['start', 'end', 'year']);
        });
    }
};
