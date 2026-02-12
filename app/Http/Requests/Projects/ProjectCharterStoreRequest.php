<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCharterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'background' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
            'impact_value' => ['nullable', 'string'],
            'key_personnel' => ['nullable', 'string'],
            'key_items' => ['nullable', 'string'],
            'budget' => ['nullable', 'string', 'max:255'],
            'risks_identified' => ['nullable', 'string'],
            'risk_mitigation' => ['nullable', 'string'],
        ];
    }
}
