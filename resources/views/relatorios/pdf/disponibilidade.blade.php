<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Disponibilidade</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .resumo { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de Disponibilidade de Declarações</h2>
        <p>Data: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Instituição</th>
                <th>Tipo</th>
                <th>Faixa</th>
                <th>Total</th>
                <th>Utilizadas</th>
                <th>Disponíveis</th>
                <th>Data Entrega</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribuicoes as $distribuicao)
            <tr>
                <td>{{ $distribuicao->instituicao->nome }}</td>
                <td>{{ $distribuicao->tipo_certidao == 'obito' ? 'DO' : 'DNV' }}</td>
                <td>{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</td>
                <td>{{ $distribuicao->total_certidoes }}</td>
                <td>{{ $distribuicao->utilizadas }}</td>
                <td>{{ $distribuicao->disponiveis }}</td>
                <td>{{ $distribuicao->data_entrega->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="resumo">
        <h3>Resumo Geral</h3>
        <p>Total de distribuições: {{ $distribuicoes->count() }}</p>
        <p>Total de declarações: {{ $distribuicoes->sum('total_certidoes') }}</p>
        <p>Total utilizadas: {{ $distribuicoes->sum('utilizadas') }}</p>
        <p>Total disponíveis: {{ $distribuicoes->sum('disponiveis') }}</p>
    </div>
</body>
</html>
