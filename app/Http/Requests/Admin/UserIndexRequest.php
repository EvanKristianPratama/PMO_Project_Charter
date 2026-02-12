<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['all', 'pending', 'approved', 'rejected'])],
            'app_role' => ['nullable', Rule::in(['all', User::APP_ROLE_ADMIN, User::APP_ROLE_USER])],
            'permission_role' => [
                'nullable',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === 'all') {
                        return;
                    }

                    if (! Role::query()->where('name', $value)->exists()) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
        ];
    }
}
