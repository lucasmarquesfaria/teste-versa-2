<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pendências de Distribuição') }}
            </h2>
            <x-secondary-button-link href="{{ route('distribuicoes.index') }}">
                <x-icons.arrow-left class="h-4 w-4 mr-1" />
                {{ __('Voltar para Distribuições') }}
            </x-secondary-button-link>
        </div>
    </x-slot>

    <x-layouts.main-page titulo="Pendências" descricao="Acompanhe as distribuições com formulários pendentes de baixa">
        <x-slot name="filtros">
            <x-page-filter-panel :route="route('distribuicoes.pendencias')">
                <div>
                    <x-input-label for="instituicao_id" :value="__('Instituição')" />
                    <x-select-input id="instituicao_id" name="instituicao_id" class="mt-1 block w-full">
                        <option value="">Todas</option>
                        @foreach($instituicoes as $instituicao)
                            <option value="{{ $instituicao->id }}" @selected(request('instituicao_id') == $instituicao->id)>
                                {{ $instituicao->nome }}
                            </option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="tipo_certidao" :value="__('Tipo de Formulário')" />
                    <x-select-input id="tipo_certidao" name="tipo_certidao" class="mt-1 block w-full">
                        <option value="">Todos</option>
                        <option value="obito" @selected(request('tipo_certidao') == 'obito')>Declaração de Óbito (DO)</option>
                        <option value="nascidos_vivos" @selected(request('tipo_certidao') == 'nascidos_vivos')>Declaração de Nascidos Vivos (DNV)</option>
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="data_inicio" :value="__('Data Início')" />
                    <x-date-input id="data_inicio" name="data_inicio" :value="request('data_inicio')" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="data_fim" :value="__('Data Fim')" />
                    <x-date-input id="data_fim" name="data_fim" :value="request('data_fim')" class="mt-1 block w-full" />
                </div>
            </x-page-filter-panel>
        </x-slot>

        <x-slot name="alertas">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icons.exclamation class="h-5 w-5 text-yellow-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Resumo de Pendências
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Total de distribuições com pendências: <span class="font-bold">{{ $distribuicoes->count() }}</span></p>
                            <p>Total de formulários pendentes: <span class="font-bold">{{ $distribuicoes->sum('quantidade_pendentes') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-data-table :data="$distribuicoes">
            <x-slot name="header">
                <x-table.th label="Instituição" />
                <x-table.th label="Tipo" />
                <x-table.th label="Numeração" />
                <x-table.th label="Data Entrega" />
                <x-table.th label="Total" />
                <x-table.th label="Pendentes" />
                <x-table.th label="Ações" />
            </x-slot>

            <x-slot name="row">
                @foreach($distribuicoes as $distribuicao)
                    <tr>
                        <x-table.td>{{ $distribuicao->instituicao->nome }}</x-table.td>
                        <x-table.td>
                            @if($distribuicao->tipo_certidao == 'obito')
                                <x-status-badge type="info">DO</x-status-badge>
                            @else
                                <x-status-badge type="info">DNV</x-status-badge>
                            @endif
                        </x-table.td>
                        <x-table.td>{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</x-table.td>
                        <x-table.td>@data($distribuicao->data_entrega)</x-table.td>
                        <x-table.td>{{ $distribuicao->total_certidoes }}</x-table.td>
                        <x-table.td status="danger">
                            {{ $distribuicao->quantidade_pendentes }}
                            <span class="text-xs text-gray-500">
                                ({{ number_format(($distribuicao->quantidade_pendentes / $distribuicao->total_certidoes) * 100, 1) }}%)
                            </span>
                        </x-table.td>
                        <x-table.td>
                            <div class="flex space-x-2">
                                @can('distribuicao_visualizar')
                                <x-action-link href="{{ route('distribuicoes.show', $distribuicao) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Detalhes
                                </x-action-link>
                                @endcan
                                
                                @can('baixa_criar')
                                <x-action-link href="{{ route('baixas.create', ['distribuicao_id' => $distribuicao->id]) }}" class="text-green-600 hover:text-green-900">
                                    Nova Baixa
                                </x-action-link>
                                @endcan
                                
                                @can('baixa_criar')
                                <x-action-link href="{{ route('baixas.create-lote', ['distribuicao_id' => $distribuicao->id]) }}" class="text-blue-600 hover:text-blue-900">
                                    Baixa em Lote
                                </x-action-link>
                                @endcan
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-slot>
        </x-data-table>
    </x-layouts.main-page>
</x-app-layout>
