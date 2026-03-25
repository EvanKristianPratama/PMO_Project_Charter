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
        return [
            'status' => ['required', 'string', 'max:255'],
            'review_status' => ['required', Rule::in(['At Risk', 'On Track', 'Not Started', 'Not Signed'])],
            'month_year' => ['required', 'date_format:Y-m'],
        ];
    }
}
