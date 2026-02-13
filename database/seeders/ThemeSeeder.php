<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get goals by code for reference
        $goalA = Goal::where('code', 'A')->first();
        $goalB = Goal::where('code', 'B')->first();
        $goalD = Goal::where('code', 'D')->first();

        // Themes for Goal A - Maximizing legacy business
        $themesGoalA = [
            'Driving O&G production growth',
            'Building Flexibility & resilience in refinery',
            'Transforming legacy retail and trading business',
            'Expanding midstream infrastructure',
            'Creating value through legacy service business',
        ];

        foreach ($themesGoalA as $themeName) {
            Theme::create([
                'idGoal' => $goalA->id,
                'name' => $themeName,
            ]);
        }

        // Themes for Goal B - Building low carbon business
        $themesGoalB = [
            'Building biofuel business',
            'Expanding petchem business',
            'Expanding Geothermal Capacity',
            'Developing other low carbon business',
        ];

        foreach ($themesGoalB as $themeName) {
            Theme::create([
                'idGoal' => $goalB->id,
                'name' => $themeName,
            ]);
        }

        // Themes for Goal C - Holding inputs/ enablers required
        $goalC = Goal::where('code', 'C')->first();

        $themesGoalC = [
            'Enhancing HSSE performance in Pertamina',
            'Enchancing people and organization',
            'Implementing digital transformation',
            'Policy advocacy',
        ];

        foreach ($themesGoalC as $themeName) {
            Theme::create([
                'idGoal' => $goalC->id,
                'name' => $themeName,
            ]);
        }

        // Themes for Goal D - Sustainability
        $themesGoalD = [
            'Decarbonizing Pertamina operations',
            'Reducing Scope 3 emissions',
            'Improving ESG rating',
        ];

        foreach ($themesGoalD as $themeName) {
            Theme::create([
                'idGoal' => $goalD->id,
                'name' => $themeName,
            ]);
        }
    }
}
