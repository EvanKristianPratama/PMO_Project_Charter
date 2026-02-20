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
        Schema::table('trs_digital_initiative', function (Blueprint $table) {
            $table->integer('value')->default(4)
                ->comment('1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)')
                ->change();
            $table->integer('urgency')->default(4)
                ->comment('1 = Low, 2 = Medium, 3 = High, 4 = TBC (To be Discussed)')
                ->change();

            $table->unsignedInteger('source_id')->after('urgency')->nullable();
            $table->foreign('source_id')
                ->references('id')
                ->on('mst_data_source')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
            $table->dropForeign(['rjpp_tagging']);
            $table->dropColumn('rjpp_tagging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_digital_initiative', function (Blueprint $table) {
            $table->integer('value')->default(999)->change();
            $table->integer('urgency')->default(999)->change();

            $table->unsignedBigInteger('rjpp_tagging')->default(999)
                ->comment('999 = TBC (To be Discussed), 1 = Low, 2 = Medium, 3 = High');

            $table->foreign('rjpp_tagging')
                ->references('id')
                ->on('trs_themes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};
