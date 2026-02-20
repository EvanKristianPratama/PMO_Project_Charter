<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coe;

class CoeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'TBC',
            'AI/Analytics',
            'IoT',
            'Cloud & Advanced Computing',
            'RPA',
            'Robotics',
        ];

        foreach ($names as $name) {
            Coe::create(['name' => $name]);
        }
    }
}
