<?php

namespace Database\Seeders;

use App\Models\DataSource;
use Illuminate\Database\Seeder;

class DataSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DataSource::updateOrCreate(
            [
                'name' => 'Pertamina Digital Transformation DT Workstream Compendium',
                'month' => 'May',
                'year' => 2024,
            ],
        );
    }
}
