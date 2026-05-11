<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use App\Models\TrsProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateITInitiativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var TrsProject|null $project */
        $project = $this->route('project');
        $latestHistoryStatusId = $project?->projectStatusHistories()->value('trs_project_status_history.status');
        $currentStatusId = is_numeric($latestHistoryStatusId) ? (int) $latestHistoryStatusId : 0;
        $statusChanged = $project !== null
            && $this->filled('status')
            && (int) $this->input('status') !== $currentStatusId;
        $statusRule = static function (string $attribute, mixed $value, \Closure $fail): void {
            $statusId = is_numeric($value) ? (int) $value : null;

            if ($statusId === 0) {
                return;
            }

            if (
                $statusId === null
                || ! \App\Models\InitiativeStatus::query()->whereKey($statusId)->exists()
            ) {
                $fail('Status project charter tidak valid.');
            }
        };

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                Rule::unique('trs_projects', 'code')->ignore($project?->id),
            ],
            'status' => ['required', 'integer', $statusRule],
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
