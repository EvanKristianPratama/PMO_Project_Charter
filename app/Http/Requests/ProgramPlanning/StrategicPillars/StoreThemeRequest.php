<?php

namespace App\Http\Requests\ProgramPlanning\StrategicPillars;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pilar = (string) $this->input('pilar');
        $goalId = $this->input('idGoal');

        return [
            'idGoal' => [
                'required',
                'integer',
                Rule::exists('mst_goals', 'id')->where(
                    fn ($query) => $query->where('pilar', $pilar)
                ),
            ],
            'theme_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('trs_themes', 'theme_number')->where(
                    fn ($query) => $query->where('idGoal', $goalId)
                ),
            ],
            'name' => ['required', 'string', 'max:255'],
            'pilar' => ['required', 'string', 'in:1,2'],
        ];
    }
}
