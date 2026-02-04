<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\AuditService;
use Illuminate\Http\Request;

/**
 * Controller para gestão de configurações pelo ADMIN.
 */
class SettingsController extends Controller
{
    protected $settings;
    protected $audit;

    public function __construct(SettingsService $settings, AuditService $audit)
    {
        $this->settings = $settings;
        $this->audit = $audit;
    }

    // Mostra a lista/valores das configurações
    public function index()
    {
        // Implementação mínima: obter algumas chaves importantes
        $data = [
            'percent_sinal' => $this->settings->get('percent_sinal', 20),
            'dias_min_pagamento_total' => $this->settings->get('dias_min_pagamento_total', 30),
        ];

        return view('admin.settings.index', compact('data'));
    }

    public function edit($key)
    {
        $value = $this->settings->get($key);
        return view('admin.settings.edit', compact('key', 'value'));
    }

    public function update(Request $request, $key)
    {
        $value = $request->input('value');
        $this->settings->set($key, $value);

        // Auditoria: grava que o ADMIN alterou uma configuração
        $this->audit->log($request->user(), 'update', 'settings', $key, ['value' => $value], $request);

        return redirect()->route('admin.settings.index')->with('status', 'Configuração atualizada');
    }
}
