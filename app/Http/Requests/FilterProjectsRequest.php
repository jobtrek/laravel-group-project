<?php

namespace App\Http\Requests;

use App\Enums\ProjectSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Every role can see every project (Source_of_truth.md: "All roles can see all projects? Yes"),
        // so this is a read-only filter with no role restriction, not a stub.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'sort' => ['nullable', Rule::enum(ProjectSort::class)],

            'score_min' => ['nullable', 'integer', 'min:0'],

            'date_from' => ['nullable', 'date'],

            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],

            'proposer_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
