<?php

namespace App\Http\Requests\ITInitiative;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ITInitiativeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', Rule::unique('trs_projects', 'code')],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'on_hold'])],
            'owner_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
