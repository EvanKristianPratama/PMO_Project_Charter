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
        $statusChanged = $project !== null
            && $this->filled('status')
            && (int) $this->input('status') !== (int) $project->status;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                Rule::unique('trs_projects', 'code')->ignore($project?->id),
            ],
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'project_status_changed_at' => [
                Rule::requiredIf($statusChanged),
                'nullable',
                'date_format:Y-m-d',
            ],
            'project_status_notes' => ['nullable', 'string', 'max:2000'],
            'owner' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'charter_category' => ['nullable', 'string', 'max:255'],
        ];
    }
}
