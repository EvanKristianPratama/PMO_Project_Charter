<?php

namespace Database\Factories;

use App\Models\ProjectCharter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectCharterFactory extends Factory
{
    protected $model = ProjectCharter::class;

    public function definition(): array
    {
        return [
            'project_id' => \App\Models\TrsProject::factory(),
            'version_label' => 'v1',
            'category' => $this->faker->word(),
            'owner' => $this->faker->name(),
            'status' => 1,
        ];
    }
}
