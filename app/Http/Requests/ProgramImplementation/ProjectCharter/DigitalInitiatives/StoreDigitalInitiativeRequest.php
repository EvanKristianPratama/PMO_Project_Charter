<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDigitalInitiativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('trs_projects', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'charter_category' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'project_status_changed_at' => ['required', 'date_format:Y-m-d'],
            'project_status_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
