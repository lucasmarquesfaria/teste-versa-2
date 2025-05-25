<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Baixa') }}
            </h2>
            <div class="flex space-x-2">
                @can('baixa_editar')
                <a href="{{ route('baixas.edit', $baixa) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Baixa</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Instituição:</p>
                            <p>{{ $baixa->distribuicao->instituicao->nome }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Formulário:</p>
                            <p>
                                @if($baixa->distribuicao->tipo_certidao == 'obito')
                                    Declaração de Óbito (DO)
                                @else
                                    Declaração de Nascidos Vivos (DNV)
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Faixa da Distribuição:</p>
                            <p>{{ $baixa->distribuicao->numero_inicial }} a {{ $baixa->distribuicao->numero_final }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Número da Declaração:</p>
                            <p class="font-semibold text-lg">{{ $baixa->numero }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Data de Devolução:</p>
                            <p>@data($baixa->data_devolucao)</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Situação:</p>
                            <p>
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
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registrado por:</p>
                            <p>{{ $baixa->usuario->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Data de Registro:</p>
                            <p>{{ $baixa->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Última Atualização:</p>
                            <p>{{ $baixa->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        @if($baixa->observacao)
                        <div class="md:col-span-2 lg:col-span-3">
                            <p class="text-sm font-medium text-gray-500">Observações:</p>
                            <p class="whitespace-pre-line">{{ $baixa->observacao }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
