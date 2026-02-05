<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório de Receitas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Relatório de Receitas</h2>
    <p>Período: {{ $from }} a {{ $to }}</p>

    <p>
        Sinal: {{ number_format($sinal, 2, ',', '.') }} |
        Total: {{ number_format($total, 2, ',', '.') }} |
        Geral: {{ number_format($geral, 2, ',', '.') }}
    </p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Reserva</th>
            <th>Valor</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->reservation_id ?? '-' }}</td>
                <td>{{ number_format((float)$p->valor, 2, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
