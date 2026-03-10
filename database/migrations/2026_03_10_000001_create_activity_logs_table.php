<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('event');                        // login, logout, created, updated, deleted
            $table->string('subject_type')->nullable();     // App\Models\MstInitiative, dll
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable();    // label human-readable record yang diubah
            $table->text('description')->nullable();
            $table->json('properties')->nullable();         // { old: {...}, new: {...} }
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
