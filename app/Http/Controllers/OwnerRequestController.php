<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoOwner;
use App\Models\User;
use Illuminate\Http\Request;

class OwnerRequestController extends Controller
{
    private array $provincias = [
        'Bengo','Benguela','Bié','Cabinda','Cuando Cubango','Cuanza Norte','Cuanza Sul',
        'Cunene','Huambo','Huíla','Luanda','Lunda Norte','Lunda Sul','Malanje','Moxico',
        'Namibe','Uíge','Zaire','Icolo e Bengo','Moxico Leste','Cuando'
    ];

    public function create()
    {
        return view('owner.request', [
            'provincias' => $this->provincias
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'nome' => [
                    'required',
                    'string',
                    'max:120',
                    'regex:/^[A-Za-zÀ-ÿ\s]+$/'
                ],
                'email' => [
                    'required',
                    'email',
                    'max:150'
                ],
                'telefone' => [
                    'nullable',
                    'regex:/^9\d{8}$/'
                ],
                'nif' => [
                    'nullable',
                    'regex:/^\d+$/',
                    'max:50'
                ],
                'nome_salao' => [
                    'required',
                    'string',
                    'max:150'
                ],
                'provincia' => [
                    'required',
                    'in:' . implode(',', $this->provincias)
                ],
                'municipio' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[A-Za-zÀ-ÿ\s]+$/'
                ],
            ],
            [
                'nome.required' => 'Informe o seu nome completo.',
                'nome.regex' => 'O nome deve conter apenas letras e espaços.',
                'nome.max' => 'O nome não pode ultrapassar 120 caracteres.',

                'email.required' => 'Informe um endereço de email.',
                'email.email' => 'Informe um email válido.',
                'email.max' => 'O email não pode ultrapassar 150 caracteres.',

                'telefone.regex' => 'O número de telemóvel deve começar com 9 e conter exatamente 9 dígitos.',

                'nif.regex' => 'O NIF deve conter apenas números.',
                'nif.max' => 'O NIF não pode ultrapassar 50 caracteres.',

                'nome_salao.required' => 'Informe o nome do salão.',
                'nome_salao.max' => 'O nome do salão não pode ultrapassar 150 caracteres.',

                'provincia.required' => 'Selecione a província.',
                'provincia.in' => 'A província selecionada não é válida.',

                'municipio.required' => 'Selecione o município.',
                'municipio.regex' => 'O município deve conter apenas letras e espaços.',
                'municipio.max' => 'O município não pode ultrapassar 100 caracteres.',
            ]
        );

        if (User::where('email', $data['email'])->exists()) {
            return back()->withErrors([
                'email' => 'Este email já se encontra registado no sistema.'
            ])->withInput();
        }

        if (
            SolicitacaoOwner::where('email', $data['email'])
                ->where('estado', SolicitacaoOwner::PENDENTE)
                ->exists()
        ) {
            return back()->withErrors([
                'email' => 'Já existe uma solicitação pendente associada a este email.'
            ])->withInput();
        }

        SolicitacaoOwner::create([
            'user_id'    => null,
            'nome'       => $data['nome'],
            'email'      => $data['email'],
            'telefone'   => $data['telefone'] ?? null,
            'nif'        => $data['nif'] ?? null,
            'nome_salao' => $data['nome_salao'],
            'provincia'  => $data['provincia'],
            'municipio'  => $data['municipio'],
        ]);

        return redirect()->route('owner.request.sent');
    }

    public function sent()
    {
        return view('owner.sent');
    }
}
