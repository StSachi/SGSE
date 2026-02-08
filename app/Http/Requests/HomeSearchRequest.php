<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // público
    }

    public function rules(): array
    {
        return [
            'q'         => ['nullable', 'string', 'max:80'],
            'provincia' => ['nullable', 'string', 'max:80'],
            'cidade'    => ['nullable', 'string', 'max:80'],
            'data'      => ['nullable', 'date'],
            'capMin'    => ['nullable', 'integer', 'min:0', 'max:100000'],
            'precoMax'  => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'onlyAvailable' => ['nullable', 'boolean'],
        ];
    }
}
