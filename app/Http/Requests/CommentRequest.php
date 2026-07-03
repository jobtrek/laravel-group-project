<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\States\EncoursState;
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

        if (! $project instanceof Project || ! $project->status instanceof EncoursState) {
            return false;
        }

        return (bool) $this->user()?->hasRole(['chef_de_projet', 'collaborateur']);
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
