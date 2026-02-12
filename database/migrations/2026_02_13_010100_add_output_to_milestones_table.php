<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trs_milestones', function (Blueprint $table): void {
            $table->text('output')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('trs_milestones', function (Blueprint $table): void {
            $table->dropColumn('output');
        });
    }
};
