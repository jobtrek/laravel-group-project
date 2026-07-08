<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasScoringRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PropositionRequest extends FormRequest
{
    use HasScoringRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create project');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'buts' => ['required', 'array', 'min:1'],
            'buts.*' => ['required', 'string', 'max:255'],
            'perimetre' => ['required', 'string'],

            'phases' => ['required', 'array', 'min:1'],
            'phases.*.titre' => ['required', 'string', 'max:255'],
            'phases.*.duree' => ['required', 'string', 'max:100'],
            'phases.*.description' => ['required', 'string'],
            'phases.*.objectifs' => ['required', 'array', 'min:1'],
            'phases.*.objectifs.*' => ['required', 'string'],
            'phases.*.livrables' => ['required', 'array', 'min:1'],
            'phases.*.livrables.*' => ['required', 'string'],
            'phases.*.ressources_necessaires' => ['required', 'array', 'min:1'],
            'phases.*.ressources_necessaires.*.resource_type' => ['required', 'string', 'max:100'],
            'phases.*.ressources_necessaires.*.amount_needed' => ['required', 'numeric', 'min:0'],

            'ressources_totales' => ['nullable', 'string'],

            ...$this->scoringRules(),
        ];
    }
}
