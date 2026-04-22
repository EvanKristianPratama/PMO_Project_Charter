<?php

namespace App\Http\Requests\ProgramPlanning\StrategicHouse\BusinessStrategy;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateBusinessStrategyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.id' => ['required', 'integer', 'exists:trs_business_strategy,id'],
            'rows.*.business_unit' => ['required', 'integer', 'exists:trs_organization,id'],
            'rows.*.maximazing_value' => ['nullable', 'string', 'max:5000'],
            'rows.*.expand' => ['nullable', 'string', 'max:5000'],
            'rows.*.low_carbon' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
