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
            'status' => ['required', 'integer', Rule::exists('trs_status_initiative', 'id')],
            'owner_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
