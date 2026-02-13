<?php

namespace App\Http\Requests\ITInitiative;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ITInitiativeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                Rule::unique('trs_projects', 'code')->ignore($project?->id),
            ],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'on_hold'])],
            'owner_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
