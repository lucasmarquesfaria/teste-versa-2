<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- CARD DE PENDÊNCIAS EM ATRASO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Cards de Indicadores (KPIs) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Instituições</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalInstituicoes }}</p>
                        <p class="text-sm text-gray-500 mt-2">Total de instituições cadastradas</p>
                    </div>
                </div>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1">Pendências</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $totalPendencias }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            Certidões sem baixa: {{ number_format(($totalPendencias / ($totalCertidoes ?: 1)) * 100, 1) }}%
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-1 flex items-center justify-between">
                            Pendências em Atraso
                            @if(isset($distribuicoesPendentesVencidas) && $distribuicoesPendentesVencidas->count() > 0)
                                <button id="btn-pendencias-atraso" class="ml-2 px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver detalhes
                                </button>
                            @endif
                        </h3>
                        <p class="text-3xl font-bold text-red-600">{{ $distribuicoesPendentesVencidas->count() ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-2">Distribuições com prazo de devolução vencido</p>
                    </div>
                </div>
            </div>

            {{-- MODAL DE PENDÊNCIAS EM ATRASO --}}
            {{-- Overlay do modal --}}
            <div id="overlay-pendencias-atraso" class="fixed inset-0 bg-black opacity-30 z-40 hidden" aria-hidden="true"></div>
            
            {{-- Modal --}}
            <div id="modal-pendencias-atraso" class="fixed z-50 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 pointer-events-none">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 relative pointer-events-auto">
                        <button id="fechar-modal-pendencias" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                        <h2 class="text-xl font-bold mb-4 text-red-700">Pendências em Atraso</h2>
                        
                        {{-- Filtros --}}
                        <div class="mb-4 flex flex-wrap items-center gap-4">
                            <div class="flex-grow">
                                <input type="text" id="filtro-texto" placeholder="Buscar instituição..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <select id="filtro-tipo" class="px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Todos os tipos</option>
                                    <option value="DO">DO</option>
                                    <option value="DNV">DNV</option>
                                </select>
                            </div>
                            <div>
                                <select id="filtro-ordem" class="px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="prazo">Ordenar por prazo</option>
                                    <option value="instituicao">Ordenar por instituição</option>
                                    <option value="tipo">Ordenar por tipo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto max-h-96">
                            <table id="tabela-pendencias" class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2">Instituição</th>
                                        <th class="px-4 py-2">Tipo</th>
                                        <th class="px-4 py-2">Numeração</th>
                                        <th class="px-4 py-2">Prazo Limite</th>
                                        <th class="px-4 py-2">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($distribuicoesPendentesVencidas as $d)
                                        <tr class="item-pendencia" data-instituicao="{{ $d->instituicao->nome }}" data-tipo="{{ $d->tipo_certidao == 'obito' ? 'DO' : 'DNV' }}" data-prazo="{{ $d->data_limite_baixa->format('Y-m-d') }}">
                                            <td class="px-4 py-2">{{ $d->instituicao->nome }}</td>
                                            <td class="px-4 py-2">{{ $d->tipo_certidao == 'obito' ? 'DO' : 'DNV' }}</td>
                                            <td class="px-4 py-2">{{ $d->numero_inicial }} a {{ $d->numero_final }}</td>
                                            <td class="px-4 py-2 text-red-700">{{ $d->data_limite_baixa->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2">
                                                <a href="{{ route('baixas.create', ['distribuicao_id' => $d->id]) }}" class="text-blue-600 hover:text-blue-900">Registrar Baixa</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="sem-pendencias"><td colspan="5" class="text-center text-gray-500 py-4">Nenhuma pendência em atraso.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Controles básicos do modal
                    const btn = document.getElementById('btn-pendencias-atraso');
                    const modal = document.getElementById('modal-pendencias-atraso');
                    const overlay = document.getElementById('overlay-pendencias-atraso');
                    const fechar = document.getElementById('fechar-modal-pendencias');
                    
                    if(btn && modal && fechar && overlay) {
                        // Abrir o modal
                        btn.addEventListener('click', function() {
                            modal.classList.remove('hidden');
                            overlay.classList.remove('hidden');
                            // Inicializar os filtros quando abrir o modal
                            inicializarFiltros();
                        });
                        
                        // Fechar o modal
                        fechar.addEventListener('click', function() {
                            modal.classList.add('hidden');
                            overlay.classList.add('hidden');
                        });
                        
                        // Fechar ao clicar fora do modal
                        modal.addEventListener('click', function(e) {
                            if(e.target === modal) {
                                modal.classList.add('hidden');
                                overlay.classList.add('hidden');
                            }
                        });
                        
                        // Fechar ao clicar no overlay
                        overlay.addEventListener('click', function() {
                            modal.classList.add('hidden');
                            overlay.classList.add('hidden');
                        });
                    }
                    
                    // Função para inicializar os filtros e a ordenação
                    function inicializarFiltros() {
                        const filtroTexto = document.getElementById('filtro-texto');
                        const filtroTipo = document.getElementById('filtro-tipo');
                        const filtroOrdem = document.getElementById('filtro-ordem');
                        const itensPendencia = document.querySelectorAll('.item-pendencia');
                        const semPendencias = document.getElementById('sem-pendencias');
                        
                        // Limpar os filtros
                        if(filtroTexto) filtroTexto.value = '';
                        if(filtroTipo) filtroTipo.value = '';
                        if(filtroOrdem) filtroOrdem.value = 'prazo';
                        
                        // Mostrar todos os itens inicialmente
                        itensPendencia.forEach(item => {
                            item.style.display = '';
                        });
                        
                        if(semPendencias && itensPendencia.length > 0) {
                            semPendencias.style.display = 'none';
                        }
                        
                        // Configurar os eventos de filtragem
                        if(filtroTexto) {
                            filtroTexto.addEventListener('input', aplicarFiltros);
                        }
                        
                        if(filtroTipo) {
                            filtroTipo.addEventListener('change', aplicarFiltros);
                        }
                        
                        if(filtroOrdem) {
                            filtroOrdem.addEventListener('change', aplicarFiltros);
                        }
                        
                        // Função para aplicar todos os filtros
                        function aplicarFiltros() {
                            let encontrouAlgum = false;
                            const texto = filtroTexto ? filtroTexto.value.toLowerCase() : '';
                            const tipo = filtroTipo ? filtroTipo.value : '';
                            
                            // Filtragem
                            itensPendencia.forEach(item => {
                                const instituicao = item.dataset.instituicao.toLowerCase();
                                const tipoItem = item.dataset.tipo;
                                
                                // Verificar se o item atende aos critérios de filtro
                                const matchTexto = !texto || instituicao.includes(texto);
                                const matchTipo = !tipo || tipoItem === tipo;
                                
                                // Mostrar ou esconder com base nos filtros
                                if (matchTexto && matchTipo) {
                                    item.style.display = '';
                                    encontrouAlgum = true;
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                            
                            // Exibir mensagem se nenhum item corresponder aos filtros
                            if (semPendencias) {
                                semPendencias.style.display = encontrouAlgum ? 'none' : '';
                            }
                            
                            // Ordenação
                            ordenarTabela();
                        }
                        
                        // Função para ordenar a tabela
                        function ordenarTabela() {
                            if (!filtroOrdem) return;
                            
                            const tabela = document.getElementById('tabela-pendencias');
                            if (!tabela) return;
                            
                            const tbody = tabela.querySelector('tbody');
                            if (!tbody) return;
                            
                            const ordem = filtroOrdem.value;
                            
                            // Converter NodeList para Array para ordenação
                            const linhas = Array.from(itensPendencia).filter(item => item.style.display !== 'none');
                            
                            // Ordenar pelo critério selecionado
                            linhas.sort((a, b) => {
                                if (ordem === 'instituicao') {
                                    return a.dataset.instituicao.localeCompare(b.dataset.instituicao);
                                } else if (ordem === 'tipo') {
                                    return a.dataset.tipo.localeCompare(b.dataset.tipo);
                                } else { // prazo (padrão)
                                    return a.dataset.prazo.localeCompare(b.dataset.prazo);
                                }
                            });
                            
                            // Reordenar as linhas no DOM
                            linhas.forEach(linha => {
                                tbody.appendChild(linha);
                            });
                        }
                    }
                });
            </script>
            {{-- FIM CARD/MODAL PENDÊNCIAS EM ATRASO --}}

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
