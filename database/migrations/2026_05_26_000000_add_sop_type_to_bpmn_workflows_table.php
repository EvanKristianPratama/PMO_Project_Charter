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
        Schema::table('bpmn_workflows', function (Blueprint $table) {
            if (!Schema::hasColumn('bpmn_workflows', 'sop_type')) {
                $table->string('sop_type', 1)->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpmn_workflows', function (Blueprint $table) {
            if (Schema::hasColumn('bpmn_workflows', 'sop_type')) {
                $table->dropColumn('sop_type');
            }
        });
    }
};
