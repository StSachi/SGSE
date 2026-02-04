<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação/actualização de Venue (salão).
 * Contém regras de validação e mensagens em português.
 */
class VenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização é tratada pelo middleware RBAC; permitimos aqui para simplificar
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:191'],
            'descricao' => ['nullable', 'string'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'municipio' => ['nullable', 'string', 'max:100'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'capacidade' => ['nullable', 'integer', 'min:1'],
            'preco_base' => ['nullable', 'numeric', 'min:0'],
            'regras_texto' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do salão é obrigatório.',
            'images.max' => 'Só é permitido enviar até 5 imagens por pedido.',
            'images.*.image' => 'Cada ficheiro deve ser uma imagem válida.',
        ];
    }
}
