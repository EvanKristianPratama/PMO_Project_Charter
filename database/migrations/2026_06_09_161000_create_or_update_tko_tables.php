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
        // 1. Handle trs_tko_sections table
        if (!Schema::hasTable('trs_tko_sections')) {
            Schema::create('trs_tko_sections', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->integer('order')->nullable();
                $table->unsignedInteger('regulation_id')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('trs_tko_sections', function (Blueprint $table) {
                if (!Schema::hasColumn('trs_tko_sections', 'regulation_id')) {
                    $table->unsignedInteger('regulation_id')->nullable()->after('order');
                }
                if (!Schema::hasColumn('trs_tko_sections', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
                if (!Schema::hasColumn('trs_tko_sections', 'order')) {
                    $table->integer('order')->nullable()->after('name');
                }
            });
        }

        // 2. Handle trs_tko_content table
        if (!Schema::hasTable('trs_tko_content')) {
            Schema::create('trs_tko_content', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('regulation_id')->nullable();
                $table->unsignedInteger('section_id')->nullable();
                $table->text('content')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('trs_tko_content', function (Blueprint $table) {
                if (!Schema::hasColumn('trs_tko_content', 'regulation_id')) {
                    $table->unsignedInteger('regulation_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('trs_tko_content', 'section_id')) {
                    $table->unsignedInteger('section_id')->nullable()->after('regulation_id');
                }
                if (!Schema::hasColumn('trs_tko_content', 'content')) {
                    $table->text('content')->nullable()->after('section_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trs_tko_sections', function (Blueprint $table) {
            if (Schema::hasColumn('trs_tko_sections', 'regulation_id')) {
                $table->dropColumn('regulation_id');
            }
        });

        Schema::table('trs_tko_content', function (Blueprint $table) {
            if (Schema::hasColumn('trs_tko_content', 'regulation_id')) {
                $table->dropColumn('regulation_id');
            }
        });
    }
};
