<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && $project->proposer_id === $this->user()?->id;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'corrections' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'corrections.required' => 'Vous devez soumettre les corrections avant de continuer.',
            'corrections.min' => 'Vous devez corriger au moins un champ.',
        ];
    }
}
