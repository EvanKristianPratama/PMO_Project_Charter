<?php

namespace App\Http\Requests\ProgramPlanning\StrategicPillars;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pilar = (string) $this->input('pilar');

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mst_goals', 'code')->where(
                    fn ($query) => $query->where('pilar', $pilar)
                ),
            ],
            'title' => ['required', 'string', 'max:255'],
            'pilar' => ['required', 'string', 'in:1,2'],
        ];
    }
}
