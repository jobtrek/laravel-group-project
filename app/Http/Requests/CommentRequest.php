<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\EvaluationState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        if (! $project instanceof Project) {
            return false;
        }

        if ($project->status instanceof EvaluationState) {
            return (bool) $this->user()?->hasRole('direction');
        }

        if ($project->status instanceof EncoursState) {
            return (bool) $this->user()?->hasRole('chef_de_projet');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'stage' => ['required', 'string', 'max:50'],
        ];
    }
}
