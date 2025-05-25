<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios') }}
        </h2>
    </x-slot>    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('Relatórios do Sistema') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Relatório de Distribuição -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Relatório de Distribuição</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório detalhado das distribuições realizadas, permitindo filtrar por instituição, tipo de formulário e período.
                        </p>
                        
                        <form action="{{ route('relatorios.distribuicao') }}" method="GET" target="_blank" class="space-y-4" id="form-distribuicao">
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
                                    <x-text-input id="distribuicao_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="distribuicao-data-inicio-error"></div>
                                </div>
                                <div>
                                    <x-input-label for="distribuicao_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="distribuicao_data_fim" name="data_fim" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="distribuicao-data-fim-error"></div>
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
                                <x-primary-button type="button" onclick="validateAndSubmit('form-distribuicao')">
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
                        
                        <form action="{{ route('relatorios.utilizacao') }}" method="GET" target="_blank" class="space-y-4" id="form-utilizacao">
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
                                    <x-text-input id="utilizacao_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="utilizacao-data-inicio-error"></div>
                                </div>
                                <div>
                                    <x-input-label for="utilizacao_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="utilizacao_data_fim" name="data_fim" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="utilizacao-data-fim-error"></div>
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
                                <x-primary-button type="button" onclick="validateAndSubmit('form-utilizacao')">
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
                        
                        <form action="{{ route('relatorios.pendencias') }}" method="GET" target="_blank" class="space-y-4" id="form-pendencias">
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
                                    <x-text-input id="pendencias_data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="pendencias-data-inicio-error"></div>
                                </div>
                                <div>
                                    <x-input-label for="pendencias_data_fim" :value="__('Data Fim')" />
                                    <x-text-input id="pendencias_data_fim" name="data_fim" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="pendencias-data-fim-error"></div>
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
                                <x-primary-button type="button" onclick="validateAndSubmit('form-pendencias')">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            
                <!-- Relatório de Vendas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Relatório de Vendas') }}</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório detalhado de vendas no período selecionado.
                        </p>
                        
                        <form method="POST" action="{{ route('relatorio.vendas') }}" target="_blank" class="space-y-4" id="form-vendas">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="dataInicial" :value="__('Data Inicial')" />
                                    <x-text-input id="dataInicial" name="dataInicial" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="vendas-data-inicio-error"></div>
                                    <x-input-error class="mt-2" :messages="$errors->get('dataInicial')" />
                                </div>
                                <div>
                                    <x-input-label for="dataFinal" :value="__('Data Final')" />
                                    <x-text-input id="dataFinal" name="dataFinal" type="date" class="mt-1 block w-full" required />
                                    <div class="text-red-500 text-sm hidden error-message" id="vendas-data-fim-error"></div>
                                    <x-input-error class="mt-2" :messages="$errors->get('dataFinal')" />
                                </div>
                            </div>
                            <div>
                                <x-primary-button type="button" onclick="validateAndSubmit('form-vendas')">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Relatório de Disponibilidade -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Relatório de Disponibilidade') }}</h3>
                        <p class="text-gray-600 mb-4">
                            Gera um relatório sobre a disponibilidade atual de formulários no sistema.
                        </p>
                        
                        <form method="POST" action="{{ route('relatorio.disponibilidade') }}" target="_blank" class="space-y-4">
                            @csrf
                            <div class="text-gray-600">
                                Este relatório não requer parâmetros adicionais e mostrará o estado atual do sistema.
                            </div>
                            <div>
                                <x-primary-button type="submit">
                                    {{ __('Gerar Relatório') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>        </div>
    </div>

    <script>
        function validateAndSubmit(formId) {
            const form = document.getElementById(formId);
            const isValid = validateDates(form);
            
            if (isValid) {
                form.submit();
            }
        }

        function validateDates(form) {
            // Limpa mensagens de erro anteriores
            const errorMessages = form.querySelectorAll('.error-message');
            errorMessages.forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            
            // Identifica os campos de data
            let dataInicio, dataFim, dataInicioErrorId, dataFimErrorId;
            
            if (form.id === 'form-distribuicao') {
                dataInicio = document.getElementById('distribuicao_data_inicio');
                dataFim = document.getElementById('distribuicao_data_fim');
                dataInicioErrorId = 'distribuicao-data-inicio-error';
                dataFimErrorId = 'distribuicao-data-fim-error';
            } else if (form.id === 'form-utilizacao') {
                dataInicio = document.getElementById('utilizacao_data_inicio');
                dataFim = document.getElementById('utilizacao_data_fim');
                dataInicioErrorId = 'utilizacao-data-inicio-error';
                dataFimErrorId = 'utilizacao-data-fim-error';
            } else if (form.id === 'form-pendencias') {
                dataInicio = document.getElementById('pendencias_data_inicio');
                dataFim = document.getElementById('pendencias_data_fim');
                dataInicioErrorId = 'pendencias-data-inicio-error';
                dataFimErrorId = 'pendencias-data-fim-error';
            } else if (form.id === 'form-vendas') {
                dataInicio = document.getElementById('dataInicial');
                dataFim = document.getElementById('dataFinal');
                dataInicioErrorId = 'vendas-data-inicio-error';
                dataFimErrorId = 'vendas-data-fim-error';
            }
            
            // Se não encontrou campos de data, não há validação a fazer
            if (!dataInicio || !dataFim) {
                return true;
            }
            
            let isValid = true;
            
            // Verifica se os campos estão preenchidos
            if (!dataInicio.value) {
                displayError(dataInicioErrorId, 'A data inicial é obrigatória.');
                isValid = false;
            }
            
            if (!dataFim.value) {
                displayError(dataFimErrorId, 'A data final é obrigatória.');
                isValid = false;
            }
            
            // Se ambas as datas estão preenchidas, verifica se a data final não é menor que a inicial
            if (dataInicio.value && dataFim.value) {
                const inicio = new Date(dataInicio.value);
                const fim = new Date(dataFim.value);
                
                if (fim < inicio) {
                    displayError(dataFimErrorId, 'A data final não pode ser anterior à data inicial.');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        function displayError(errorElementId, message) {
            const errorElement = document.getElementById(errorElementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
