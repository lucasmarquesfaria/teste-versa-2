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
                                                        <a href="{{ route('baixas.create', ['distribuicao_id' => $distribuicao->id]) }}" class="text-blue-600 hover:text-blue-900">Registrar Devolução</a>
                                                        @endcan
                                                        
                                                        @can('baixa_criar')
                                                        <a href="{{ route('baixas.create-lote', ['distribuicao_id' => $distribuicao->id]) }}" class="text-cyan-600 hover:text-cyan-900">Devolução em Lote</a>
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

                    <div class="mt-10">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Declarações Não Devolvidas</h3>
                        <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
                            <div>
                                <label for="tipo_certidao" class="block text-xs font-medium text-gray-600">Tipo de Certidão</label>
                                <select name="tipo_certidao" id="tipo_certidao" class="mt-1 block border-gray-300 rounded shadow-sm">
                                    <option value="">Todas</option>
                                    <option value="obito" {{ request('tipo_certidao') == 'obito' ? 'selected' : '' }}>Óbito (DO)</option>
                                    <option value="nascidos_vivos" {{ request('tipo_certidao') == 'nascidos_vivos' ? 'selected' : '' }}>Nascidos Vivos (DNV)</option>
                                </select>
                            </div>
                            <div>
                                <label for="pendentes" class="block text-xs font-medium text-gray-600">Status</label>
                                <select name="pendentes" id="pendentes" class="mt-1 block border-gray-300 rounded shadow-sm">
                                    <option value="">Todas</option>
                                    <option value="1" {{ request('pendentes') === '1' ? 'selected' : '' }}>Apenas com pendentes</option>
                                    <option value="0" {{ request('pendentes') === '0' ? 'selected' : '' }}>Apenas totalmente baixadas</option>
                                </select>
                            </div>
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">Filtrar</button>
                        </form>
                        @php
                            $distribuicoesFiltradas = $instituicao->distribuicoes->filter(function($d) {
                                if(request('tipo_certidao') && $d->tipo_certidao !== request('tipo_certidao')) return false;
                                if(request('pendentes') === '1' && count($d->numeros_pendentes) === 0) return false;
                                if(request('pendentes') === '0' && count($d->numeros_pendentes) > 0) return false;
                                return true;
                            });
                        @endphp
                        @forelse($distribuicoesFiltradas as $distribuicao)
                            <div class="mb-4 p-4 bg-gray-50 rounded border">
                                <div class="mb-2 font-semibold">
                                    {{ $distribuicao->tipo_certidao == 'obito' ? 'DO' : 'DNV' }} - {{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }} ({{ $distribuicao->data_entrega->format('d/m/Y') }})
                                </div>
                                @if(count($distribuicao->numeros_pendentes) > 0)
                                    <div class="text-sm text-gray-700">
                                        <span class="font-medium">Números pendentes:</span>
                                        <span class="break-all">{{ implode(', ', $distribuicao->numeros_pendentes) }}</span>
                                    </div>
                                @else
                                    <div class="text-green-700 text-sm">Todos os formulários foram devolvidos.</div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">Nenhuma distribuição encontrada com os filtros selecionados.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
