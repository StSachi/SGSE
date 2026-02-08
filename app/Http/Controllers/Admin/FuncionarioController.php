<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FuncionarioController extends Controller
{
    public function index()
    {
        $funcionarios = User::query()
            ->where('papel', 'FUNCIONARIO')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.funcionarios.index', compact('funcionarios'));
    }

    public function create()
    {
        return view('admin.funcionarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'ativo'    => ['nullable', 'boolean'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'papel'    => 'FUNCIONARIO',
            'ativo'    => $request->boolean('ativo', true),
        ]);

        return redirect()
            ->route('admin.funcionarios.index')
            ->with('success', 'Funcionário criado com sucesso.');
    }

    public function show(User $funcionario)
    {
        $this->abortIfNotFuncionario($funcionario);

        return view('admin.funcionarios.show', compact('funcionario'));
    }

    public function edit(User $funcionario)
    {
        $this->abortIfNotFuncionario($funcionario);

        return view('admin.funcionarios.edit', compact('funcionario'));
    }

    public function update(Request $request, User $funcionario)
    {
        $this->abortIfNotFuncionario($funcionario);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($funcionario->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'ativo'    => ['nullable', 'boolean'],
        ]);

        $funcionario->name  = $data['name'];
        $funcionario->email = $data['email'];
        $funcionario->ativo = $request->boolean('ativo', true);

        if (! empty($data['password'])) {
            $funcionario->password = Hash::make($data['password']);
        }

        $funcionario->save();

        return redirect()
            ->route('admin.funcionarios.index')
            ->with('success', 'Funcionário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $funcionario)
    {
        $this->abortIfNotFuncionario($funcionario);

        $request->validate([
            'confirm' => ['accepted'],
        ], [
            'confirm.accepted' => 'Confirme a eliminação para continuar.',
        ]);

        $funcionario->delete();

        return redirect()
            ->route('admin.funcionarios.index')
            ->with('success', 'Funcionário eliminado com sucesso.');
    }

    private function abortIfNotFuncionario(User $user): void
    {
        abort_if($user->papel !== 'FUNCIONARIO', 404);
    }
}
