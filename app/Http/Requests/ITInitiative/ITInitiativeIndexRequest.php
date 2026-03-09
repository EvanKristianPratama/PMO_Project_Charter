<?php

namespace App\Http\Requests\ITInitiative;

use App\Models\InitiativeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ITInitiativeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $allowedStatusIds = InitiativeStatus::ordered()
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->prepend(0)
            ->unique()
            ->values()
            ->all();

        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'integer', Rule::in($allowedStatusIds)],
            'month' => ['nullable', 'date_format:m'],
        ];
    }
}
