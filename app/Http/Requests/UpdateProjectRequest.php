<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasScoringRules;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    use HasScoringRules;

    public function rules(): array
    {
        /** @var Project $project */
        $project = $this->route('project');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'perimetre' => ['nullable', 'string'],
            'but' => ['required', 'array', 'min:1'],
            'but.*' => ['required', 'string', 'max:500'],
            'membres' => ['required', 'array', 'min:1'],
            'membres.*' => ['required', 'integer', 'distinct', 'exists:users,id'],

            'phases' => ['required', 'array', 'min:1'],
            'phases.*.id' => ['nullable', 'integer', 'exists:project_phases,id'],
            'phases.*.titre' => ['required', 'string', 'max:255'],
            'phases.*.duree' => ['required', 'string', 'max:255'],
            'phases.*.description' => ['required', 'string'],
            'phases.*.objectifs' => ['required', 'array', 'min:1'],
            'phases.*.objectifs.*' => ['required', 'string', 'max:500'],
            'phases.*.livrables' => ['required', 'array', 'min:1'],
            'phases.*.livrables.*' => ['required', 'string', 'max:500'],
            'phases.*.ressources' => ['sometimes', 'array'],
            'phases.*.ressources.*.id' => ['nullable', 'integer', 'exists:phase_resources,id'],
            'phases.*.ressources.*.resource_type' => ['required', 'string', 'max:255'],
            'phases.*.ressources.*.amount_needed' => ['required', 'numeric', 'min:0'],

            ...$this->scoringRules(),
        ];

        return $rules;
    }
}
