<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreITInitiativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $statusSelected = $this->filled('status') && (int) $this->input('status') > 0;
        $statusRule = static function (string $attribute, mixed $value, \Closure $fail): void {
            $statusId = is_numeric($value) ? (int) $value : null;

            if ($statusId === 0) {
                return;
            }

            if (
                $statusId === null
                || ! \Modules\ITSP\Models\InitiativeStatus::query()->whereKey($statusId)->exists()
            ) {
                $fail('Status project charter tidak valid.');
            }
        };

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', Rule::unique('trs_projects', 'code')],
            'status' => ['required', 'integer', $statusRule],
            'project_status_changed_at' => [
                Rule::requiredIf($statusSelected),
                'nullable',
                'date_format:Y-m-d',
            ],
            'project_status_notes' => ['nullable', 'string', 'max:2000'],
            'owner' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'charter_category' => ['nullable', 'string', 'max:255'],
            'initiative_ids' => ['nullable', 'array'],
            'initiative_ids.*' => [
                'integer',
                Rule::exists('mst_initiative', 'id')->where(static fn ($query) => $query->where('tipe_initiative', 2)),
            ],
        ];
    }
}
