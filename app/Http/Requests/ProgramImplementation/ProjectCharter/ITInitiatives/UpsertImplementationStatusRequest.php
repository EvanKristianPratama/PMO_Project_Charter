<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\ITInitiatives;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertImplementationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        return [
            'status' => ['required', 'string', 'max:255'],
            'review_status' => ['required', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
            'start' => ['required', Rule::in($months)],
            'end' => ['nullable', Rule::in($months)],
            'year' => ['nullable', 'string', 'size:4'],
        ];
    }
}
