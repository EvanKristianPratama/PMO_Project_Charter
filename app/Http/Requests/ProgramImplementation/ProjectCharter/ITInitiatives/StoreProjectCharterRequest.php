<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectCharterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'version_label' => ['nullable', 'string', 'max:255'],
            'sponsor' => ['nullable', 'string', 'max:255'],
            'owner' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'leader' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'integer'],
            'tgl_dokumen' => ['nullable', 'date'],
            'category' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'start_year' => ['nullable', 'digits:4'],
            'end_year' => ['nullable', 'digits:4'],
            'background' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'impact_value' => ['nullable', 'string'],
            'key_personnel' => ['nullable', 'string'],
            'key_items' => ['nullable', 'string'],
            'budget' => ['nullable', 'string', 'max:255'],
            'key_milestone' => ['nullable', 'string'],
            'risks_identified' => ['nullable', 'string'],
            'risk_mitigation' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'target_kpi' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
