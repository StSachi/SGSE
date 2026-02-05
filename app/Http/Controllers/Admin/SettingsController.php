<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

/**
 * Gestão de configurações (ADMIN)
 *
 * ERS:
 * - Apenas ADMIN pode alterar configurações globais
 * - Alterações devem ser auditáveis
 */
class SettingsController extends Controller
{
    /**
     * Lista de chaves permitidas (whitelist)
     */
    private const ALLOWED_KEYS = [
        'percent_sinal',
        'dias_min_pagamento_total',
    ];

    public function __construct(protected SettingsService $settings) {}

    private function assertAdmin(Request $request): void
    {
        $user = $request->user();

        if (
            ! $user
            || ! method_exists($user, 'isAdmin')
            || ! $user->isAdmin()
            || (isset($user->ativo) && ! $user->ativo)
        ) {
            abort(403);
        }
    }

    private function assertKeyAllowed(string $key): void
    {
        if (! in_array($key, self::ALLOWED_KEYS, true)) {
            abort(404);
        }
    }

    // Lista/valores das configurações
    public function index(Request $request)
    {
        $this->assertAdmin($request);

        $data = [
            'percent_sinal' => (float) $this->settings->get('percent_sinal', 20),
            'dias_min_pagamento_total' => (int) $this->settings->get('dias_min_pagamento_total', 30),
        ];

        // Auditoria de acesso à tela de settings
        $this->audit(
            'settings_view',
            'settings',
            null,
            ['keys' => array_keys($data)],
            $request->ip()
        );

        return view('admin.settings.index', compact('data'));
    }

    public function edit(Request $request, string $key)
    {
        $this->assertAdmin($request);
        $this->assertKeyAllowed($key);

        $value = $this->settings->get($key);

        // Auditoria de acesso à edição
        $this->audit(
            'settings_edit_view',
            'settings',
            null,
            ['key' => $key],
            $request->ip()
        );

        return view('admin.settings.edit', compact('key', 'value'));
    }

    public function update(Request $request, string $key)
    {
        $this->assertAdmin($request);
        $this->assertKeyAllowed($key);

        // validação por chave (ERS)
        $rules = match ($key) {
            'percent_sinal' => ['required', 'numeric', 'min:0', 'max:100'],
            'dias_min_pagamento_total' => ['required', 'integer', 'min:0', 'max:3650'],
            default => ['required'],
        };

        $validated = $request->validate([
            'value' => $rules,
        ]);

        $value = $validated['value'];

        $this->settings->set($key, $value);

        // Auditoria: ADMIN alterou configuração
        $this->audit(
            'settings_update',
            'settings',
            null,
            ['key' => $key, 'value' => $value],
            $request->ip()
        );

        return redirect()
            ->route('admin.settings.index')
            ->with('status', __('messages.settings_updated'));
    }
}
