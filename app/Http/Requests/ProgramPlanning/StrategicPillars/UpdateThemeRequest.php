<?php

namespace App\Http\Requests\ProgramPlanning\StrategicPillars;

use App\Models\Theme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Theme|null $theme */
        $theme = $this->route('theme');
        $pilar = (string) $this->input('pilar', $theme?->goal?->pilar);
        $goalId = $this->input('idGoal', $theme?->idGoal);

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
                Rule::unique('trs_themes', 'theme_number')
                    ->ignore($theme?->id)
                    ->where(fn ($query) => $query->where('idGoal', $goalId)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'pilar' => ['required', 'string', 'in:1,2'],
        ];
    }
}
