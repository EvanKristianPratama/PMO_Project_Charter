<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhaseDigital;

class PhaseDigitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = [
            ['name' => 'propose'],
            ['name' => 'review'],
            ['name' => 'approve'],
            ['name' => 'finish'],
        ];

        foreach ($phases as $phase) {
            PhaseDigital::create($phase);
        }
    }
}

