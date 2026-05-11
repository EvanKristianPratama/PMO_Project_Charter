<?php

namespace App\Http\Requests\ProgramImplementation\Roadmap;

use Illuminate\Foundation\Http\FormRequest;

class RoadmapPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pc_id' => ['nullable', 'integer', 'min:1'],
            'project_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function projectCharterId(): ?int
    {
        return $this->positiveInteger('pc_id');
    }

    public function legacyProjectId(): ?int
    {
        return $this->positiveInteger('project_id');
    }

    private function positiveInteger(string $key): ?int
    {
        $value = $this->integer($key);

        return $value > 0 ? $value : null;
    }
}
