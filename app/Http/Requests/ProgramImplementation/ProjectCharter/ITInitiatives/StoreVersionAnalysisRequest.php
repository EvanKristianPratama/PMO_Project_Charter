<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use Illuminate\Foundation\Http\FormRequest;

class StoreVersionAnalysisRequest extends FormRequest
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
            'leader' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'tgl_dokumen' => ['nullable', 'string'],
            'target_kpi' => ['nullable', 'string'],
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
        ];
    }
}
