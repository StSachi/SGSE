<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de reservas.
 * Garante data válida (formato Y-m-d) e não no passado.
 */
class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'venue_id' => ['required', 'exists:venues,id'],
            'data_evento' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_evento.after_or_equal' => 'A data do evento não pode ser no passado.',
            'venue_id.exists' => 'Salão inválido.',
        ];
    }
}
