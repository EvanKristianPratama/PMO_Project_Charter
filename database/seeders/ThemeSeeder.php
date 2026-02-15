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
        $goalC = Goal::where('code', 'C')->first();
        $goalD = Goal::where('code', 'D')->first();

        // Themes for Goal A - Maximizing legacy business
        $themesGoalA = [
            ['theme_number' => 1, 'name' => 'Driving O&G production growth'],
            ['theme_number' => 2, 'name' => 'Building Flexibility & resilience in refinery'],
            ['theme_number' => 3, 'name' => 'Transforming legacy retail and trading business'],
            ['theme_number' => 4, 'name' => 'Expanding midstream infrastructure'],
            ['theme_number' => 5, 'name' => 'Creating value through legacy service business'],
        ];

        foreach ($themesGoalA as $themeName) {
            Theme::create([
                'idGoal' => $goalA->id,
                'theme_number' => $themeName['theme_number'],
                'name' => $themeName['name'],
            ]);
        }

        // Themes for Goal B - Building low carbon business
        $themesGoalB = [
            ['theme_number' => 6, 'name' => 'Building biofuel business'],
            ['theme_number' => 7, 'name' => 'Expanding petchem business'],
            ['theme_number' => 8, 'name' => 'Expanding Geothermal Capacity'],
            ['theme_number' => 9, 'name' => 'Developing other low carbon business'],
        ];

        foreach ($themesGoalB as $themeName) {
            Theme::create([
                'idGoal' => $goalB->id,
                'theme_number' => $themeName['theme_number'],
                'name' => $themeName['name'],
            ]);
        }        

        // Themes for Goal C - Holding inputs/ enablers required
        $themesGoalC = [
            ['theme_number' => 10, 'name' => 'Enhancing HSSE performance in Pertamina'],
            ['theme_number' => 11, 'name' => 'Enchancing people and organization'],
            ['theme_number' => 12, 'name' => 'Implementing digital transformation'],
            ['theme_number' => 13, 'name' => 'Policy advocacy'],
        ];

        foreach ($themesGoalC as $themeName) {
            Theme::create([
                'idGoal' => $goalC->id,
                'theme_number' => $themeName['theme_number'],
                'name' => $themeName['name'],
            ]);
        }

        // Themes for Goal D - Sustainability
        $themesGoalD = [
            ['theme_number' => 14, 'name' => 'Decarbonizing Pertamina operations'],
            ['theme_number' => 15, 'name' => 'Reducing Scope 3 emissions'],
            ['theme_number' => 16, 'name' => 'Improving ESG rating'],
        ];

        foreach ($themesGoalD as $themeName) {
            Theme::create([
                'idGoal' => $goalD->id,
                'theme_number' => $themeName['theme_number'],
                'name' => $themeName['name'],
            ]);
        }
    }
}
