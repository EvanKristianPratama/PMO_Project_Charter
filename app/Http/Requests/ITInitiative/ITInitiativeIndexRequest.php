<?php

namespace App\Http\Requests\ITInitiative;

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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['draft', 'active', 'completed', 'on_hold'])],
        ];
    }
}
