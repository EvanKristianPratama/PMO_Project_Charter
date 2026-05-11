<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:100', 'exists:permissions,name'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = preg_replace('/\s+/', ' ', trim((string) $this->input('name')));

        $this->merge([
            'name' => $name,
        ]);
    }
}
