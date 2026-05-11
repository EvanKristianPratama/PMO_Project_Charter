<?php

namespace App\Http\Requests\ProgramPlanning\InitiativeRelation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInitiativeRelationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'model_relasi' => ['required', 'string', 'max:50'],
            'initiative_code_row' => ['required', 'integer', 'exists:mst_initiative,id', 'different:initiative_code_column'],
            'initiative_code_column' => ['required', 'integer', 'exists:mst_initiative,id'],
            'type_relation' => ['required', 'integer', Rule::in([1, 2])],
            'justifikasi' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
