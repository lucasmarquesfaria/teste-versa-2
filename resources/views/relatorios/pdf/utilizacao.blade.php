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
        .situacao {
            background-color: #f9f9f9;
            padding: 6px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            font-style: italic;
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
        .status-utilizada {
            color: green;
        }
        .status-cancelada {
            color: red;
        }
        .status-nao-utilizada {
            color: #555;
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
    
    @if($baixasAgrupadas->count() > 0)
        @foreach($baixasAgrupadas as $instituicaoNome => $grupoSituacao)
            <div class="instituicao">Instituição: {{ $instituicaoNome }}</div>
            
            @foreach($grupoSituacao as $situacao => $baixas)
                <div class="situacao">
                    @if($situacao == 'utilizada')
                        <span class="status-utilizada">Utilizadas ({{ $baixas->count() }})</span>
                    @elseif($situacao == 'cancelada')
                        <span class="status-cancelada">Canceladas ({{ $baixas->count() }})</span>
                    @else
                        <span class="status-nao-utilizada">Não Utilizadas ({{ $baixas->count() }})</span>
                    @endif
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Número</th>
                            <th>Data Devolução</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($baixas as $baixa)
                            <tr>
                                <td>
                                    @if($baixa->distribuicao->tipo_certidao == 'obito')
                                        DO
                                    @else
                                        DNV
                                    @endif
                                </td>
                                <td>{{ $baixa->numero }}</td>
                                <td>{{ $baixa->data_devolucao->format('d/m/Y') }}</td>
                                <td>{{ $baixa->observacao ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
            
            <div style="text-align: right; margin-bottom: 15px;">
                <strong>Total:</strong> {{ $grupoSituacao->flatten()->count() }} baixas
            </div>
        @endforeach
        
        <!-- Totalizadores gerais -->
        <div class="totais">
            <div class="total-item">Total de Baixas: {{ $totais['total'] }}</div>
            <div class="total-item">
                <span class="status-utilizada">Utilizadas: {{ $totais['utilizada'] }}</span> | 
                <span class="status-cancelada">Canceladas: {{ $totais['cancelada'] }}</span> | 
                <span class="status-nao-utilizada">Não Utilizadas: {{ $totais['nao_utilizada'] }}</span>
            </div>
        </div>
    @else
        <p>Nenhuma baixa encontrada com os filtros especificados.</p>
    @endif
    
    <div class="footer">
        <p>Sistema de Controle de DOs e DNVs - Página 1</p>
    </div>
</body>
</html>
