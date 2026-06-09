<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PropositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'porteur' => ['required', 'string', 'max:255'],
            'membres' => ['required', 'array', 'min:1'],
            'membres.*' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'buts' => ['required', 'array', 'min:1'],
            'buts.*' => ['required', 'string', 'max:255'],
            'perimetre' => ['required', 'string'],

            'phases' => ['required', 'array', 'min:1'],
            'phases.*.titre' => ['required', 'string', 'max:255'],
            'phases.*.duree' => ['required', 'string', 'max:255'],
            'phases.*.description' => ['required', 'string'],
            'phases.*.objectifs' => ['required', 'array', 'min:1'],
            'phases.*.objectifs.*' => ['required', 'string'],
            'phases.*.livrables' => ['required', 'array', 'min:1'],
            'phases.*.livrables.*' => ['required', 'string'],
            'phases.*.ressources_necessaires' => ['required', 'array', 'min:1'],
            'phases.*.ressources_necessaires.*' => ['required', 'string'],

            'ressources_totales' => ['nullable', 'string'],

            'portee' => ['required', 'integer', 'min:0', 'max:50'],
            'impact' => ['required', 'integer', 'min:1', 'max:5'],
            'confiance' => ['required', 'integer', 'min:0', 'max:100'],
            'effort' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }
}
