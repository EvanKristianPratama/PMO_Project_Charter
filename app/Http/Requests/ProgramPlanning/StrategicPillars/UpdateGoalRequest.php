<?php

namespace App\Http\Requests\ProgramPlanning\StrategicPillars;

use Modules\ITSP\Models\Goal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Goal|null $goal */
        $goal = $this->route('goal');
        $pilar = (string) $this->input('pilar', $goal?->pilar);

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mst_goals', 'code')
                    ->ignore($goal?->id)
                    ->where(fn ($query) => $query->where('pilar', $pilar)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'pilar' => ['required', 'string', 'in:1,2'],
        ];
    }
}
