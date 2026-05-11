<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected'])],
            'app_role' => ['sometimes', Rule::in([User::APP_ROLE_ADMIN, User::APP_ROLE_USER])],
            'permission_role' => ['sometimes', 'string', 'exists:roles,name'],
        ];
    }
}
