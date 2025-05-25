<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Indicadores (KPIs) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Card 1: Instituições -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Instituições</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalInstituicoes }}</p>
                        <p class="text-sm text-gray-500 mt-2">Total de instituições cadastradas</p>
                    </div>
                </div>
                
                <!-- Card 2: Distribuições -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Distribuições</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $totalDistribuicoes['total'] }}</p>
                        <div class="text-sm text-gray-500 mt-2">
                            <p>DOs: {{ $totalDistribuicoes['obito'] }}</p>
                            <p>DNVs: {{ $totalDistribuicoes['nascidos_vivos'] }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3: Baixas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Baixas</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalBaixas['total'] }}</p>
                        <div class="text-sm text-gray-500 mt-2">
                            <p>Utilizadas: {{ $totalBaixas['utilizada'] }}</p>
                            <p>Canceladas: {{ $totalBaixas['cancelada'] }}</p>
                            <p>Não utilizadas: {{ $totalBaixas['nao_utilizada'] }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card 4: Pendências -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Pendências</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $totalPendencias }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            Certidões sem baixa: {{ number_format(($totalPendencias / ($totalCertidoes ?: 1)) * 100, 1) }}%
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Distribuições Recentes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Distribuições Recentes</h3>
                    @if($distribuicoesRecentes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instituição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numeração</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($distribuicoesRecentes as $distribuicao)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $distribuicao->instituicao->nome }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($distribuicao->tipo_certidao == 'obito')
                                                    Declaração de Óbito (DO)
                                                @else
                                                    Declaração de Nascidos Vivos (DNV)
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">@data($distribuicao->data_entrega)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('distribuicoes.show', $distribuicao->id) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Nenhuma distribuição registrada.</p>
                    @endif
                    
                    @can('distribuicao_listar')
                        <div class="mt-4 text-right">
                            <a href="{{ route('distribuicoes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Ver todas as distribuições →</a>
                        </div>
                    @endcan
                </div>
            </div>
            
            <!-- Baixas Recentes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Baixas Recentes</h3>
                    @if($baixasRecentes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instituição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($baixasRecentes as $baixa)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $baixa->distribuicao->instituicao->nome }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($baixa->distribuicao->tipo_certidao == 'obito')
                                                    DO
                                                @else
                                                    DNV
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $baixa->numero }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">@data($baixa->data_devolucao)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($baixa->situacao == 'utilizada')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Utilizada
                                                    </span>
                                                @elseif($baixa->situacao == 'cancelada')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Cancelada
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Não Utilizada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('baixas.show', $baixa->id) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Nenhuma baixa registrada.</p>
                    @endif
                    
                    @can('baixa_listar')
                        <div class="mt-4 text-right">
                            <a href="{{ route('baixas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Ver todas as baixas →</a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
