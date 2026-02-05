<?php

use Illuminate\Contracts\Validation\Rule;

return [
    'accepted' => 'O campo :attribute deverá ser aceite.',
    'active_url' => 'O campo :attribute não é um URL válido.',
    'after' => 'O campo :attribute deverá ser uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deverá ser uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute deverá conter apenas letras.',
    'email' => 'O campo :attribute deverá conter um endereço de email válido.',
    'required' => 'O campo :attribute é obrigatório.',
    'max' => [
        'string' => 'O campo :attribute não deverá conter mais de :max caracteres.',
        'file' => 'O ficheiro :attribute não deverá ser superior a :max kilobytes.',
    ],

    'custom' => [
        'images' => [
            'max' => 'Só é permitido enviar até 5 imagens por pedido.',
            'image' => 'Cada ficheiro deve ser uma imagem válida.',
        ],
    ],

    'attributes' => [],
];
