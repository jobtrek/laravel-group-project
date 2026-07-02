<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignProjectTeamRequest extends FormRequest
{
    protected $errorBag = 'assignTeam';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('membres')) {
            $this->merge([
                'membres' => array_filter($this->input('membres', []), fn ($value) => ! is_null($value) && $value !== ''),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'leader_id' => ['required', 'integer', 'exists:users,id'],
            'membres' => ['array'],
            'membres.*' => ['integer', 'exists:users,id', 'distinct'],
        ];
    }
}
