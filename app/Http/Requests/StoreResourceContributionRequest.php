<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreResourceContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Project $project */
        $project = $this->route('project');

        abort_if(
            ! $project->status instanceof RecolteState &&
            ! $project->status instanceof EncoursState,
            404
        );

        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => ['required', 'integer', 'exists:project_phases,id'],
            'resource_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
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

            $resourceType = $this->input('resource_type');
            $resource = $phase->resources->firstWhere('resource_type', $resourceType);

            if ($resource === null) {
                $validator->errors()->add('resource_type', 'This resource type is not defined for the selected phase.');

                return;
            }

            $amount = round((float) $this->input('amount'), 2);
            $remaining = $phase->remainingFor($resource);

            if ($amount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    sprintf('This contribution exceeds what is still needed for this resource type (%.2f remaining).', $remaining)
                );
            }
        });
    }
}
