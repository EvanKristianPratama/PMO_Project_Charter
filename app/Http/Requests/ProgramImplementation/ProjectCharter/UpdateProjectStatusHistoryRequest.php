<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectStatusHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
