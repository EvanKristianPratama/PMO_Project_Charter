<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitiativeStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('trs_status_initiative')->upsert([
            ['id' => 1, 'name' => 'drafting', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'propose', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'review', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'approve', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'baseline', 'created_at' => now(), 'updated_at' => now()],
        ], ['id'], ['name', 'updated_at']);
    }
}
