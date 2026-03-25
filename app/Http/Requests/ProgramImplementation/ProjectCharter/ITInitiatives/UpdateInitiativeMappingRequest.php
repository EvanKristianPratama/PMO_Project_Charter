<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInitiativeMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'initiative_ids' => ['nullable', 'array'],
            'initiative_ids.*' => [
                'integer',
                Rule::exists('mst_initiative', 'id')->where(static fn ($query) => $query->where('tipe_initiative', 2)),
            ],
        ];
    }
}
