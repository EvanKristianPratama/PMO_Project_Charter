<?php

namespace Database\Factories;

use App\Models\TrsProject;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrsProjectFactory extends Factory
{
    protected $model = TrsProject::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('PRJ-###')),
            'name' => $this->faker->sentence(3),
            'owner_id' => null,
            'status' => 1,
            'metadata' => null,
            'tipe_inisiative' => 2,
        ];
    }
}
