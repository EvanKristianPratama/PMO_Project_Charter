<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BackupRetentionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'retention_days' => ['required', 'integer', 'min:1', 'max:3650'],
        ];
    }
}
