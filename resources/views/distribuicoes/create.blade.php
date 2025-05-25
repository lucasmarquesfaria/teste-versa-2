<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Distribuição') }}
            </h2>
            <a href="{{ route('distribuicoes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('distribuicoes.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="instituicao_id" :value="__('Instituição')" />
                            <select id="instituicao_id" name="instituicao_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecione uma instituição</option>
                                @foreach($instituicoes as $instituicao)
                                    <option value="{{ $instituicao->id }}" {{ old('instituicao_id', request('instituicao_id')) == $instituicao->id ? 'selected' : '' }}>
                                        {{ $instituicao->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('instituicao_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo_certidao" :value="__('Tipo de Formulário')" />
                            <select id="tipo_certidao" name="tipo_certidao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecione o tipo</option>
                                <option value="obito" {{ old('tipo_certidao') == 'obito' ? 'selected' : '' }}>Declaração de Óbito (DO)</option>
                                <option value="nascidos_vivos" {{ old('tipo_certidao') == 'nascidos_vivos' ? 'selected' : '' }}>Declaração de Nascidos Vivos (DNV)</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_certidao')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="numero_inicial" :value="__('Número Inicial')" />
                                <x-text-input id="numero_inicial" name="numero_inicial" type="number" class="mt-1 block w-full" value="{{ old('numero_inicial') }}" min="1" required />
                                <x-input-error :messages="$errors->get('numero_inicial')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="numero_final" :value="__('Número Final')" />
                                <x-text-input id="numero_final" name="numero_final" type="number" class="mt-1 block w-full" value="{{ old('numero_final') }}" min="1" required />
                                <x-input-error :messages="$errors->get('numero_final')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="data_entrega" :value="__('Data de Entrega')" />
                            <x-text-input id="data_entrega" name="data_entrega" type="date" class="mt-1 block w-full" value="{{ old('data_entrega', date('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('data_entrega')" class="mt-2" />
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
        document.getElementById('numero_inicial').addEventListener('change', function() {
            let numeroInicial = parseInt(this.value);
            let numeroFinalInput = document.getElementById('numero_final');
            
            if (numeroFinalInput.value === '' || parseInt(numeroFinalInput.value) < numeroInicial) {
                numeroFinalInput.value = numeroInicial;
            }
        });
    </script>
</x-app-layout>
