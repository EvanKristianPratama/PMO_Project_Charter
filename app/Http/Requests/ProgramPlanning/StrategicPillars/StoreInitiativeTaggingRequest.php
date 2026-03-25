<?php

namespace App\Http\Requests\ProgramPlanning\StrategicPillars;

use Illuminate\Foundation\Http\FormRequest;

class StoreInitiativeTaggingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'initiative_id' => ['required', 'integer', 'exists:mst_initiative,id'],
            'themes_id' => ['nullable', 'integer', 'exists:trs_themes,id'],
            'goal' => ['nullable', 'string', 'max:255'],
        ];
    }
}
