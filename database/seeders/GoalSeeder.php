<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            [
                'code' => 'A',
                'title' => 'Maximizing legacy business',
            ],
            [
                'code' => 'B',
                'title' => 'Building low carbon business',
            ],
            [
                'code' => 'C',
                'title' => 'Holding inputs/ enablers required',
            ],
            [
                'code' => 'D',
                'title' => 'Sustainability',
            ],
        ];

        foreach ($goals as $goal) {
            Goal::updateOrCreate(
                ['code' => $goal['code']], // Find by code
                $goal // Update or create with this data
            );
        }
    }
}
