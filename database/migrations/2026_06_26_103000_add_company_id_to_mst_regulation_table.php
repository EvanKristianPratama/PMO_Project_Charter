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
        Schema::table('mst_regulation', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->nullable()->after('master_id');
            
            $table->foreign('company_id', 'fk_regulation_company')
                ->references('id')
                ->on('mst_bod')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_regulation', function (Blueprint $table) {
            $table->dropForeign('fk_regulation_company');
            $table->dropColumn('company_id');
        });
    }
};
