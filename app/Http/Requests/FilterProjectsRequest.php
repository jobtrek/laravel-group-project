<?php

namespace App\Http\Requests;

use App\Enums\ProjectSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort'        => ['nullable', Rule::enum(ProjectSort::class)],
            'score_min'   => ['nullable', 'integer', 'min:0'],
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
            'proposer_id' => [
    'nullable',
    Rule::when(
        fn($input) => ! in_array($input->proposer_id, ['all', 'mine']),
        ['integer', 'exists:users,id']
    ),
],
        ];
    }
}