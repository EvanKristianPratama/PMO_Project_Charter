<?php

namespace App\Http\Requests\ProgramPlanning\StrategicHouse\BusinessStrategy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessStrategyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_unit' => ['required', 'integer', 'exists:trs_organization,id'],
            'maximazing_value' => ['nullable', 'string', 'max:5000'],
            'expand' => ['nullable', 'string', 'max:5000'],
            'low_carbon' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
