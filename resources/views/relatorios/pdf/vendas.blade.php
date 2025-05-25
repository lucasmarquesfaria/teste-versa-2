<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Vendas</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .periodo { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .instituicao { margin-top: 30px; margin-bottom: 10px; color: #333; }
        .total { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de Declarações Utilizadas</h2>
        @if($dataInicial && $dataFinal)
        <div class="periodo">
            Período: {{ \Carbon\Carbon::parse($dataInicial)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFinal)->format('d/m/Y') }}
        </div>
        @endif
    </div>

    @foreach($baixas as $instituicao => $baixasInstituicao)
    <div class="instituicao">
        <h3>{{ $instituicao }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Número</th>
                    <th>Data Devolução</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($baixasInstituicao as $baixa)
                <tr>
                    <td>{{ $baixa->distribuicao->tipo_certidao == 'obito' ? 'DO' : 'DNV' }}</td>
                    <td>{{ $baixa->numero }}</td>
                    <td>{{ $baixa->data_devolucao->format('d/m/Y') }}</td>
                    <td>{{ $baixa->usuario->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            Total de declarações utilizadas: {{ $baixasInstituicao->count() }}
        </div>
    </div>
    @endforeach
</body>
</html>
