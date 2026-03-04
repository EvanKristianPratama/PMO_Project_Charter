<?php

namespace App\Http\Requests\ITInitiative;

use App\Models\TrsProject;
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
        /** @var TrsProject|null $project */
        $project = $this->route('project');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                Rule::unique('trs_projects', 'code')->ignore($project?->id),
            ],
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'owner' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'charter_category' => ['nullable', 'string', 'max:255'],
        ];
    }
}
