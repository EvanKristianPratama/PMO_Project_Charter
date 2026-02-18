<?php

namespace Database\Seeders;

use App\Models\Groub;
use App\Models\Company;
use Illuminate\Database\Seeder;

class GroubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pertamina = Company::where('name', 'PT. Pertamina')->first();

        $groubPertamina = [
            ['name' => 'Holding'],
            ['name' => 'Sub Holding']
        ];

        foreach ($groubPertamina as $groub) {
            Groub::create([
                'company_id' => $pertamina->id,
                'name' => $groub['name'],
            ]);
        }
    }
}
