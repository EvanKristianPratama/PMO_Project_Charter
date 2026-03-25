<?php

namespace App\Http\Requests\MasterData\BusinessCapability;

use Illuminate\Foundation\Http\FormRequest;

class UpsertBusinessCapabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_business' => ['nullable', 'string', 'max:255'],
            'group_function' => ['nullable', 'string', 'max:255'],
            'subGroup_function' => ['nullable', 'string', 'max:255'],
            'subSubGroup_function' => ['nullable', 'string', 'max:255'],
        ];
    }
}
