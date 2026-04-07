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
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $statuses = ['On Track', 'Done', 'At Risk', 'Delayed', 'Not Started'];

        return [
            'target' => ['required', 'integer', 'between:0,100'],
            'progress' => ['required', 'integer', 'between:0,100'],
            'month' => ['nullable', Rule::in($months)],
            'year' => ['required', 'integer', 'between:0,9999'],
            'status' => ['required', Rule::in($statuses)],
            'description' => ['nullable', 'string'],
        ];
    }
}
