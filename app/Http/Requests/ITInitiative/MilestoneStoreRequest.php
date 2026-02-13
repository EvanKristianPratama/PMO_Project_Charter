<?php

namespace App\Http\Requests\ITInitiative;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MilestoneStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'output' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
