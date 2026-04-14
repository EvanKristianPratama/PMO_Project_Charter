<?php

namespace App\Http\Requests\ProgramPlanning\StrategicHouse;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'initiative_type' => ['nullable', 'integer', 'in:1,2'],
            'show_empty' => ['nullable', 'boolean'],
            'pilar' => ['nullable', 'string'],
            'goal_id' => ['nullable', 'integer'],
            'org_id' => ['nullable', 'integer'],
        ];
    }

    public function filters(): array
    {
        return [
            'initiative_type' => in_array($this->integer('initiative_type'), [1, 2], true)
                ? $this->integer('initiative_type')
                : 1,
            'show_empty' => $this->has('show_empty')
                ? $this->boolean('show_empty')
                : true,
            'pilar' => $this->input('pilar'),
            'goal_id' => $this->input('goal_id'),
            'org_id' => $this->input('org_id'),
        ];
    }
}
