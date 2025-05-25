<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Relatório de Distribuição -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Relatório de Distribuição</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório detalhado das distribuições realizadas, permitindo filtrar por instituição, tipo de formulário e período.
                        </p>
                        
                        <form action="{{ route('relatorios.distribuicao') }}" method="GET" target="_blank" class="space-y-4">
                            <div>
                                <x-input-label for="distribuicao_instituicao_id" :value="__('Instituição')" />
                                <select id="distribuicao_instituicao_id" name="instituicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todas</option>
                                    @foreach($instituicoes as $instituicao)
                                        <option value="{{ $instituicao->id }}">{{ $instituicao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="distribuicao_tipo_certidao" :value="__('Tipo de Formulário')" />
                                <select id="distribuicao_tipo_certidao" name="tipo_certidao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="obito">Declaração de Óbito (DO)</option>
                                    <option value="nascidos_vivos">Declaração de Nascidos Vivos (DNV)</option>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="distribuicao_data_inicio" :value="__('Data Início')" />
                                    <x-text-input id="distribuicao_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" />
                                </div>
                                
                                <div>
                                    <x-input-label for="distribuicao_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="distribuicao_data_fim" name="data_fim" type="date" class="mt-1 block w-full" />
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="distribuicao_tipo_saida" :value="__('Formato de Saída')" />
                                <div class="mt-1 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="visualizar" class="form-radio" checked>
                                        <span class="ml-2">Visualizar</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="baixar" class="form-radio">
                                        <span class="ml-2">Baixar</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Relatório de Utilização -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Relatório de Utilização</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório sobre a utilização de formulários, agrupados por situação de baixa e instituição.
                        </p>
                        
                        <form action="{{ route('relatorios.utilizacao') }}" method="GET" target="_blank" class="space-y-4">
                            <div>
                                <x-input-label for="utilizacao_instituicao_id" :value="__('Instituição')" />
                                <select id="utilizacao_instituicao_id" name="instituicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todas</option>
                                    @foreach($instituicoes as $instituicao)
                                        <option value="{{ $instituicao->id }}">{{ $instituicao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="utilizacao_tipo_certidao" :value="__('Tipo de Formulário')" />
                                <select id="utilizacao_tipo_certidao" name="tipo_certidao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="obito">Declaração de Óbito (DO)</option>
                                    <option value="nascidos_vivos">Declaração de Nascidos Vivos (DNV)</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="utilizacao_situacao" :value="__('Situação')" />
                                <select id="utilizacao_situacao" name="situacao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todas</option>
                                    <option value="utilizada">Utilizadas</option>
                                    <option value="cancelada">Canceladas</option>
                                    <option value="nao_utilizada">Não Utilizadas</option>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="utilizacao_data_inicio" :value="__('Data Início')" />
                                    <x-text-input id="utilizacao_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" />
                                </div>
                                
                                <div>
                                    <x-input-label for="utilizacao_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="utilizacao_data_fim" name="data_fim" type="date" class="mt-1 block w-full" />
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="utilizacao_tipo_saida" :value="__('Formato de Saída')" />
                                <div class="mt-1 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="visualizar" class="form-radio" checked>
                                        <span class="ml-2">Visualizar</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="baixar" class="form-radio">
                                        <span class="ml-2">Baixar</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Relatório de Pendências -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Relatório de Pendências</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório com todas as pendências de baixa, agrupadas por instituição.
                        </p>
                        
                        <form action="{{ route('relatorios.pendencias') }}" method="GET" target="_blank" class="space-y-4">
                            <div>
                                <x-input-label for="pendencias_instituicao_id" :value="__('Instituição')" />
                                <select id="pendencias_instituicao_id" name="instituicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todas</option>
                                    @foreach($instituicoes as $instituicao)
                                        <option value="{{ $instituicao->id }}">{{ $instituicao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="pendencias_tipo_certidao" :value="__('Tipo de Formulário')" />
                                <select id="pendencias_tipo_certidao" name="tipo_certidao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="obito">Declaração de Óbito (DO)</option>
                                    <option value="nascidos_vivos">Declaração de Nascidos Vivos (DNV)</option>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="pendencias_data_inicio" :value="__('Data Início')" />
                                    <x-text-input id="pendencias_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" />
                                </div>
                                
                                <div>
                                    <x-input-label for="pendencias_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="pendencias_data_fim" name="data_fim" type="date" class="mt-1 block w-full" />
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="pendencias_tipo_saida" :value="__('Formato de Saída')" />
                                <div class="mt-1 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="visualizar" class="form-radio" checked>
                                        <span class="ml-2">Visualizar</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="tipo_saida" value="baixar" class="form-radio">
                                        <span class="ml-2">Baixar</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Relatório de Vendas -->
            <div class="p-6 text-gray-900">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Relatório de Vendas') }}</h3>
                        <form method="POST" action="{{ route('relatorio.vendas') }}" target="_blank">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="dataInicial" :value="__('Data Inicial')" />
                                    <x-text-input id="dataInicial" name="dataInicial" type="date" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('dataInicial')" />
                                </div>
                                <div>
                                    <x-input-label for="dataFinal" :value="__('Data Final')" />
                                    <x-text-input id="dataFinal" name="dataFinal" type="date" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('dataFinal')" />
                                </div>
                            </div>
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Relatório de Disponibilidade -->
            <div class="p-6 text-gray-900">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Relatório de Disponibilidade') }}</h3>
                        <form method="POST" action="{{ route('relatorio.disponibilidade') }}" target="_blank">
                            @csrf
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
