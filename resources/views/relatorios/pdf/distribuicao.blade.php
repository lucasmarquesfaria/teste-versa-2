<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        .cabecalho {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
            font-size: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .filtros {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .filtro {
            display: inline-block;
            margin-right: 15px;
        }
        .filtro-label {
            font-weight: bold;
        }
        .instituicao {
            background-color: #f5f5f5;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .totais {
            margin-top: 20px;
            text-align: right;
        }
        .total-item {
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="cabecalho">
        <h1>{{ $titulo }}</h1>
        <div>Data de Geração: {{ $dataGeracao->format('d/m/Y H:i') }}</div>
    </div>
    
    @if(count($filtros) > 0)
    <div class="filtros">
        <div style="font-weight: bold; margin-bottom: 5px;">Filtros aplicados:</div>
        @foreach($filtros as $label => $valor)
            <div class="filtro">
                <span class="filtro-label">{{ $label }}:</span>
                <span>{{ $valor }}</span>
            </div>
        @endforeach
    </div>
    @endif
    
    @if($distribuicoesAgrupadas->count() > 0)
        @foreach($distribuicoesAgrupadas as $instituicaoNome => $distribuicoes)
            <div class="instituicao">Instituição: {{ $instituicaoNome }}</div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Numeração</th>
                        <th>Data Entrega</th>
                        <th>Quantidade</th>
                        <th>Baixas</th>
                        <th>Pendentes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distribuicoes as $distribuicao)
                        <tr>
                            <td>
                                @if($distribuicao->tipo_certidao == 'obito')
                                    DO
                                @else
                                    DNV
                                @endif
                            </td>
                            <td>{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</td>
                            <td>{{ $distribuicao->data_entrega->format('d/m/Y') }}</td>
                            <td>{{ $distribuicao->total_certidoes }}</td>
                            <td>{{ $distribuicao->quantidade_baixas }}</td>
                            <td>{{ $distribuicao->quantidade_pendentes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Totalizadores por instituição -->
            <div style="text-align: right; margin-bottom: 15px;">
                <strong>Total de Distribuições:</strong> {{ $distribuicoes->count() }} |
                <strong>Total de Formulários:</strong> {{ $distribuicoes->sum(function($d) { return ($d->numero_final - $d->numero_inicial) + 1; }) }}
            </div>
        @endforeach
        
        <!-- Totalizadores gerais -->
        <div class="totais">
            <div class="total-item">Total Geral de Distribuições: {{ $totalDistribuicoes }}</div>
            <div class="total-item">Total Geral de Formulários: {{ $totalFormularios }}</div>
        </div>
    @else
        <p>Nenhuma distribuição encontrada com os filtros especificados.</p>
    @endif
    
    <div class="footer">
        <p>Sistema de Controle de DOs e DNVs - Página 1</p>
    </div>
</body>
</html>
