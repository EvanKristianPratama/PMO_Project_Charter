<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives;

use App\Models\TrsProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDigitalInitiativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var TrsProject|null $digitalInitiative */
        $digitalInitiative = $this->route('digital_initiative');
        $currentStatusId = is_numeric($digitalInitiative?->projectStatusHistories()->value('trs_project_status_history.status'))
            ? (int) $digitalInitiative->projectStatusHistories()->value('trs_project_status_history.status')
            : 0;
        $statusChanged = $digitalInitiative !== null
            && $this->filled('status')
            && (int) $this->input('status') !== $currentStatusId;

        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('trs_projects', 'code')->ignore($digitalInitiative?->id)],
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'charter_category' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'project_status_changed_at' => [Rule::requiredIf($statusChanged), 'nullable', 'date_format:Y-m-d'],
            'project_status_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
