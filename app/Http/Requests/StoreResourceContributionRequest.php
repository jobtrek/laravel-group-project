<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreResourceContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // any authenticated user is allowed, per requirements
    }

    public function rules(): array
    {
        return [
            'phase_id' => ['required', 'integer', 'exists:project_phases,id'],
            'resource_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Project $project */
            $project = $this->route('project');

                $phase = $project->phases()
                ->with(['resources', 'contributions'])
                ->find((int) $this->input('phase_id'));

            if ($phase === null) {
                $validator->errors()->add('phase_id', 'This phase does not belong to the selected project.');

                return;
            }

            $amount = round((float) $this->input('amount'), 2);
            $remaining = round($phase->amount_needed - $phase->amount_found, 2);

            if ($amount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    sprintf('This contribution exceeds what is still needed for this phase (%.2f remaining).', $remaining)
                );
            }
        });
    }
}
