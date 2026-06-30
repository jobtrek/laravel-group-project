<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RequestMoreInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'field_comments'   => ['required', 'array', 'min:1'],

            'field_comments.*' => ['required', 'string', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'field_comments.required'   => 'Vous devez annoter au moins un champ avant de soumettre.',
            'field_comments.min'        => 'Vous devez annoter au moins un champ avant de soumettre.',
            'field_comments.*.required' => 'Chaque annotation doit contenir un commentaire.',
            'field_comments.*.min'      => 'Chaque annotation doit contenir un commentaire.',
        ];
    }
}