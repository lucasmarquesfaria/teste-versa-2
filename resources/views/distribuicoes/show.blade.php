<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Distribuição') }}
            </h2>
            <div class="flex space-x-2">
                @can('distribuicao_editar')
                <a href="{{ route('distribuicoes.edit', $distribuicao) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                
                @can('baixa_criar')
                <a href="{{ route('baixas.create', ['distribuicao_id' => $distribuicao->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Baixa
                </a>
                @endcan
                
                <a href="{{ route('distribuicoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Distribuição</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Instituição:</p>
                            <p>{{ $distribuicao->instituicao->nome }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Formulário:</p>
                            <p>
                                @if($distribuicao->tipo_certidao == 'obito')
                                    Declaração de Óbito (DO)
                                @else
                                    Declaração de Nascidos Vivos (DNV)
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Numeração:</p>
                            <p>{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Data de Entrega:</p>
                            <p>@data($distribuicao->data_entrega)</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total de Formulários:</p>
                            <p>{{ $distribuicao->total_certidoes }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Formulários com Baixa:</p>
                            <p>{{ $distribuicao->quantidade_baixas }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Formulários Pendentes:</p>
                            <p>{{ $distribuicao->quantidade_pendentes }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registrado por:</p>
                            <p>{{ $distribuicao->usuario->name }}</p>
                        </div>
                        @if($distribuicao->observacao)
                        <div class="md:col-span-2 lg:col-span-4">
                            <p class="text-sm font-medium text-gray-500">Observações:</p>
                            <p class="whitespace-pre-line">{{ $distribuicao->observacao }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Progresso -->
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500 mb-1">Progresso de Baixas:</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $porcentagem = $distribuicao->total_certidoes > 0 
                                ? ($distribuicao->quantidade_baixas / $distribuicao->total_certidoes) * 100 
                                : 0;
                            @endphp
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $porcentagem }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-right">{{ number_format($porcentagem, 1) }}% completo</p>
                    </div>
                </div>
            </div>
            
            <!-- Baixas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Baixas Registradas</h3>
                        
                        @can('baixa_criar')
                        <div class="flex space-x-2">
                            <a href="{{ route('baixas.create', ['distribuicao_id' => $distribuicao->id]) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Baixa
                            </a>
                            
                            <a href="{{ route('baixas.create-lote', ['distribuicao_id' => $distribuicao->id]) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Baixa em Lote
                            </a>
                        </div>
                        @endcan
                    </div>
                    
                    @if($distribuicao->baixas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Devolução</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($distribuicao->baixas as $baixa)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $baixa->numero }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">@data($baixa->data_devolucao)</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $baixa->usuario->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @can('baixa_visualizar')
                                                    <a href="{{ route('baixas.show', $baixa) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                                    @endcan
                                                    
                                                    @can('baixa_editar')
                                                    <a href="{{ route('baixas.edit', $baixa) }}" class="text-green-600 hover:text-green-900">Editar</a>
                                                    @endcan
                                                    
                                                    @can('baixa_excluir')
                                                    <form action="{{ route('baixas.destroy', $baixa) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta baixa?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Nenhuma baixa registrada para esta distribuição.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
