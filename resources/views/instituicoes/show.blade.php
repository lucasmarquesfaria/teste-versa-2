<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Instituição') }}
            </h2>
            <div class="flex space-x-2">
                @can('instituicao_editar')
                <a href="{{ route('instituicoes.edit', $instituicao) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                
                <a href="{{ route('instituicoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Instituição</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nome:</p>
                                <p>{{ $instituicao->nome }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Endereço:</p>
                                <p>{{ $instituicao->endereco ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Telefone:</p>
                                <p>{{ $instituicao->telefone ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email:</p>
                                <p>{{ $instituicao->email ?: 'Não informado' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Distribuições</h3>
                            
                            @can('distribuicao_criar')
                            <a href="{{ route('distribuicoes.create', ['instituicao_id' => $instituicao->id]) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nova Distribuição
                            </a>
                            @endcan
                        </div>
                        
                        @if($instituicao->distribuicoes->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numeração</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($instituicao->distribuicoes as $distribuicao)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($distribuicao->tipo_certidao == 'obito')
                                                        Declaração de Óbito (DO)
                                                    @else
                                                        Declaração de Nascidos Vivos (DNV)
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">@data($distribuicao->data_entrega)</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $distribuicao->total_certidoes }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <div class="flex flex-col">
                                                        <span>Baixas: {{ $distribuicao->quantidade_baixas }}</span>
                                                        <span>Pendentes: {{ $distribuicao->quantidade_pendentes }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        @can('distribuicao_visualizar')
                                                        <a href="{{ route('distribuicoes.show', $distribuicao) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                                        @endcan
                                                        
                                                        @can('baixa_criar')
                                                        <a href="{{ route('baixas.create', ['distribuicao_id' => $distribuicao->id]) }}" class="text-green-600 hover:text-green-900">Nova Baixa</a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">Nenhuma distribuição registrada para esta instituição.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
