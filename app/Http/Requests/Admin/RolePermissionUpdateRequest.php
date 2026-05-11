<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RolePermissionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:100', 'exists:permissions,name'],
        ];
    }
}
