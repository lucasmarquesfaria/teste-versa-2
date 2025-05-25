<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Baixa') }}
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
                    <form method="POST" action="{{ route('baixas.update', $baixa) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label :value="__('Instituição')" />
                                <p class="mt-1 p-2 border border-gray-200 rounded-md bg-gray-50">{{ $instituicao->nome }}</p>
                            </div>
                            <div>
                                <x-input-label :value="__('Tipo de Formulário')" />
                                <p class="mt-1 p-2 border border-gray-200 rounded-md bg-gray-50">
                                    @if($distribuicao->tipo_certidao == 'obito')
                                        Declaração de Óbito (DO)
                                    @else
                                        Declaração de Nascidos Vivos (DNV)
                                    @endif
                                </p>
                            </div>
                            <div>
                                <x-input-label :value="__('Faixa de Numeração')" />
                                <p class="mt-1 p-2 border border-gray-200 rounded-md bg-gray-50">{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Número da Declaração')" />
                                <p class="mt-1 p-2 border border-gray-200 rounded-md bg-gray-50">{{ $baixa->numero }}</p>
                            </div>
                            <div>
                                <x-input-label for="data_devolucao" :value="__('Data de Devolução')" />
                                <x-text-input id="data_devolucao" name="data_devolucao" type="date" class="mt-1 block w-full" value="{{ old('data_devolucao', $baixa->data_devolucao->format('Y-m-d')) }}" required />
                                <x-input-error :messages="$errors->get('data_devolucao')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="situacao" :value="__('Situação')" />
                            <select id="situacao" name="situacao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="utilizada" {{ old('situacao', $baixa->situacao) == 'utilizada' ? 'selected' : '' }}>Utilizada</option>
                                <option value="cancelada" {{ old('situacao', $baixa->situacao) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="nao_utilizada" {{ old('situacao', $baixa->situacao) == 'nao_utilizada' ? 'selected' : '' }}>Não Utilizada</option>
                            </select>
                            <x-input-error :messages="$errors->get('situacao')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="observacao" :value="__('Observações')" />
                            <textarea id="observacao" name="observacao" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('observacao', $baixa->observacao) }}</textarea>
                            <x-input-error :messages="$errors->get('observacao')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Atualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
