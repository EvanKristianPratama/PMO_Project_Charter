<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'app_role' => ['required', Rule::in([User::APP_ROLE_USER, User::APP_ROLE_ADMIN])],
            'permission_role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }
}
