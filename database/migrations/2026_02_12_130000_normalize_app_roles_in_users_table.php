<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $column = \Illuminate\Support\Facades\Schema::hasColumn('users', 'app_role') ? 'app_role' : 'role';

        DB::table('users')
            ->whereRaw("LOWER({$column}) = ?", ['admin'])
            ->update([$column => 'admin']);

        DB::table('users')
            ->whereRaw("LOWER({$column}) = ?", ['user'])
            ->update([$column => 'user']);

        DB::table('users')
            ->where(function ($query) use ($column): void {
                $query->whereNull($column)
                    ->orWhereRaw("TRIM({$column}) = ?", [''])
                    ->orWhereRaw("LOWER({$column}) NOT IN (?, ?)", ['admin', 'user']);
            })
            ->update([$column => 'user']);
    }

    public function down(): void
    {
        // Irreversible data normalization.
    }
};
