<?php

namespace Database\Seeders;

use App\Models\Groub;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holding = Groub::where('name', 'Holding')->first();
        $subHolding = Groub::where('name', 'Sub Holding')->first();

        $organizationHolding = [
            ['name' => 'Finance'],
            ['name' => 'SS Finance'],
            ['name' => 'HC'],
            ['name' => 'SS HC'],
            ['name' => 'SPPU'],
            ['name' => 'Internal Audit'],
            ['name' => 'Risk Management'],
            ['name' => 'Integrated Logistics'],
            ['name' => 'Infrastructure'],
            ['name' => 'Asset Management'],
            ['name' => 'Procurement'],
            ['name' => 'IEDCC'],
            ['name' => 'EIT'],
            ['name' => 'ILI'],
        ];

        $organizationSubHolding = [
            ['name' => 'Upstream'],
            ['name' => 'Gas'],
            ['name' => 'PNRE'],
            ['name' => 'C&T'],
            ['name' => 'R&P'],
            ['name' => 'IML'],
        ];

        foreach ($organizationHolding as $organization) {
            Organization::create([
                'groub_id' => $holding->id,
                'name' => $organization['name'],
            ]);
        }

        foreach ($organizationSubHolding as $organization) {
            Organization::create([
                'groub_id' => $subHolding->id,
                'name' => $organization['name'],
            ]);
        }
    }
}
