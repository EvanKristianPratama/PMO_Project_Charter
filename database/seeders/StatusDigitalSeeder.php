<?php

namespace Database\Seeders;

use App\Models\StatusDigital;
use Illuminate\Database\Seeder;

class StatusDigitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusDigitals = [
            [
                'name' => 'draft',
            ],
            [
                'name' => 'purpose',
            ],
            [
                'name' => 'review',
            ],
            [
                'name' => 'approve',
            ],
            [
                'name' => 'cancel',
            ],
            [
                'name' => 'baseline',
            ],
            [
                'name' => 'TBC',
            ]
        ];

        foreach ($statusDigitals as $statusDigital) {
            StatusDigital::Create(
                ['name' => $statusDigital['name']],
                $statusDigital
            );
        }
    }
}
