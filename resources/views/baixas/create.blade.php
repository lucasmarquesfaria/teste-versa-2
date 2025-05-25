<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Baixa') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('baixas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                    <form method="POST" action="{{ route('baixas.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="instituicao_id" :value="__('Instituição')" />
                            <select id="instituicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecione uma instituição</option>
                                @foreach($instituicoes as $instituicao)
                                    <option value="{{ $instituicao->id }}" {{ old('instituicao_id', request('instituicao_id')) == $instituicao->id ? 'selected' : '' }}>
                                        {{ $instituicao->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="distribuicao_id" :value="__('Distribuição')" />
                            <select id="distribuicao_id" name="distribuicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecione uma distribuição</option>
                                @foreach($distribuicoes as $distribuicao)
                                    <option value="{{ $distribuicao->id }}" {{ old('distribuicao_id', request('distribuicao_id')) == $distribuicao->id ? 'selected' : '' }}>
                                        @if($distribuicao->tipo_certidao == 'obito')
                                            DO -
                                        @else
                                            DNV -
                                        @endif
                                        {{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}
                                        ({{ \Carbon\Carbon::parse($distribuicao->data_entrega)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('distribuicao_id')" class="mt-2" />
                            <div id="range-info" class="mt-2 text-sm text-gray-500 hidden">
                                Faixa de numeração: <span id="range-text"></span>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="numero" :value="__('Número da Declaração')" />
                            <x-text-input id="numero" name="numero" type="number" class="mt-1 block w-full" value="{{ old('numero') }}" min="1" required />
                            <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="data_devolucao" :value="__('Data de Devolução')" />
                            <x-text-input id="data_devolucao" name="data_devolucao" type="date" class="mt-1 block w-full" value="{{ old('data_devolucao', date('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('data_devolucao')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="situacao" :value="__('Situação')" />
                            <select id="situacao" name="situacao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="utilizada" {{ old('situacao') == 'utilizada' ? 'selected' : '' }}>Utilizada</option>
                                <option value="cancelada" {{ old('situacao') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="nao_utilizada" {{ old('situacao') == 'nao_utilizada' ? 'selected' : '' }}>Não Utilizada</option>
                            </select>
                            <x-input-error :messages="$errors->get('situacao')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="observacao" :value="__('Observações')" />
                            <textarea id="observacao" name="observacao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('observacao') }}</textarea>
                            <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Salvar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const instituicaoSelect = document.getElementById('instituicao_id');
            const distribuicaoSelect = document.getElementById('distribuicao_id');
            const rangeInfo = document.getElementById('range-info');
            const rangeText = document.getElementById('range-text');
            const numeroInput = document.getElementById('numero');
            
            // Função para carregar as distribuições com base na instituição selecionada
            function carregarDistribuicoes() {
                const instituicaoId = instituicaoSelect.value;
                if (!instituicaoId) {
                    distribuicaoSelect.innerHTML = '<option value="">Selecione uma distribuição</option>';
                    return;
                }
                
                // Resetar o select de distribuições
                distribuicaoSelect.disabled = true;
                distribuicaoSelect.innerHTML = '<option value="">Carregando...</option>';
                
                // Fazer a requisição AJAX para obter as distribuições
                fetch(`/distribuicoes/${instituicaoId}/get`)
                    .then(response => response.json())
                    .then(data => {
                        distribuicaoSelect.innerHTML = '<option value="">Selecione uma distribuição</option>';
                        
                        data.forEach(d => {
                            const option = document.createElement('option');
                            option.value = d.id;
                            option.textContent = d.text;
                            option.dataset.numeroInicial = d.numero_inicial;
                            option.dataset.numeroFinal = d.numero_final;
                            distribuicaoSelect.appendChild(option);
                        });
                        
                        // Verificar se existe um valor anterior selecionado
                        const oldValue = {{ old('distribuicao_id', request('distribuicao_id', 0)) }};
                        if (oldValue > 0) {
                            distribuicaoSelect.value = oldValue;
                            atualizarRangeInfo();
                        }
                        
                        distribuicaoSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Erro ao carregar distribuições:', error);
                        distribuicaoSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                        distribuicaoSelect.disabled = false;
                    });
            }
            
            // Função para atualizar a informação de range
            function atualizarRangeInfo() {
                const selectedOption = distribuicaoSelect.options[distribuicaoSelect.selectedIndex];
                
                if (selectedOption && selectedOption.value) {
                    const numeroInicial = selectedOption.dataset.numeroInicial;
                    const numeroFinal = selectedOption.dataset.numeroFinal;
                    
                    if (numeroInicial && numeroFinal) {
                        rangeText.textContent = `${numeroInicial} a ${numeroFinal}`;
                        rangeInfo.classList.remove('hidden');
                        
                        // Definir limites para o campo de número
                        numeroInput.min = numeroInicial;
                        numeroInput.max = numeroFinal;
                    } else {
                        rangeInfo.classList.add('hidden');
                    }
                } else {
                    rangeInfo.classList.add('hidden');
                }
            }
            
            // Inicializar eventos
            instituicaoSelect.addEventListener('change', carregarDistribuicoes);
            distribuicaoSelect.addEventListener('change', atualizarRangeInfo);
            
            // Se já houver uma instituição selecionada, carregar as distribuições
            if (instituicaoSelect.value) {
                carregarDistribuicoes();
            }
        });
    </script>
</x-app-layout>
