<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterProjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort'        => ['nullable', 'string', 'in:az,za,recent,oldest,importance_desc,importance_asc'],
            'score_min'   => ['nullable', 'integer', 'min:0'],
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date'],
            'proposer_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}