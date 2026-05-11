<?php

namespace App\Http\Requests\ProgramImplementation\ProjectCharter\DigitalInitiatives;

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
            'initiative_id' => [
                'required',
                'integer',
                Rule::exists('mst_initiative', 'id')->where(
                    static fn ($query) => $query->where('tipe_initiative', 1)
                ),
            ],
            'review_status' => ['required', 'string', 'max:11'],
            'pic' => ['nullable', 'string', 'max:255'],
            'start_month' => ['required', 'integer', 'between:1,12'],
            'end_month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['required', 'digits:4'],
            'status_updated' => ['required', 'string'],
        ];
    }
}
