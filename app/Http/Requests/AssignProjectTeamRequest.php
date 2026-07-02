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

    public function rules(): array
    {
        return [
            'leader_id' => ['required', 'integer', 'exists:users,id'],
            'membres' => ['array'],
            'membres.*' => ['nullable', 'integer', 'exists:users,id', 'distinct'],
        ];
    }
}
