<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório de Reservas</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        .meta { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #888; padding: 6px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>

<h1>Relatório de Reservas</h1>

<div class="meta">
    <div><strong>Período:</strong> {{ $from }} até {{ $to }}</div>
    <div><strong>Total de reservas:</strong> {{ $reservas->count() }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Espaço</th>
            <th>Data</th>
            <th>Estado</th>
            <th>Valor</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($reservas as $reserva)
            <tr>
                <td>{{ $reserva->id }}</td>

                {{-- NOME DO CLIENTE (RELACAO: client) --}}
                <td>
                    {{ $reserva->client?->name
                        ?? $reserva->client?->nome
                        ?? $reserva->cliente_nome
                        ?? '—' }}
                </td>

                {{-- ESPAÇO --}}
                <td>
                    {{ $reserva->venue?->nome
                        ?? $reserva->venue_nome
                        ?? '—' }}
                </td>

                {{-- DATA --}}
                <td>
                    {{ $reserva->data
                        ?? optional($reserva->created_at)->format('Y-m-d')
                        ?? '—' }}
                </td>

                {{-- ESTADO --}}
                <td>
                    {{ $reserva->estado
                        ?? $reserva->status
                        ?? '—' }}
                </td>

                {{-- VALOR --}}
                <td>
                    @php
                        $valor = $reserva->valor_total ?? $reserva->valor ?? null;
                    @endphp

                    {{ $valor !== null ? number_format((float)$valor, 2, ',', '.') : '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Nenhuma reserva encontrada no período.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
