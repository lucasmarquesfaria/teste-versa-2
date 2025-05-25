<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Distribuições') }}
            </h2>
            @can('distribuicao_criar')
                <x-primary-button-link href="{{ route('distribuicoes.create') }}">
                    <x-icons.plus class="h-4 w-4 mr-1" />
                    {{ __('Nova Distribuição') }}
                </x-primary-button-link>
            @endcan
        </div>
    </x-slot>

    <x-layouts.main-page titulo="Distribuições" descricao="Gerencie as distribuições de formulários">
        <x-slot name="filtros">
            <x-page-filter-panel :route="route('distribuicoes.index')">
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

        <x-data-table :data="$distribuicoes">
            <x-slot name="header">
                <x-table.th label="Instituição" />
                <x-table.th label="Tipo" />
                <x-table.th label="Numeração" />
                <x-table.th label="Data Entrega" />
                <x-table.th label="Quantidade" />
                <x-table.th label="Status" />
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
                        <x-table.td>
                            <div class="flex flex-col">
                                <div class="text-sm">
                                    <span class="font-semibold">{{ $distribuicao->quantidade_baixas }}</span> baixas
                                </div>
                                <div class="text-sm">
                                    <span class="font-semibold">{{ $distribuicao->quantidade_pendentes }}</span> pendentes
                                </div>
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <div class="flex space-x-2">
                                @can('distribuicao_visualizar')
                                    <x-action-link href="{{ route('distribuicoes.show', $distribuicao) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Visualizar
                                    </x-action-link>
                                @endcan

                                @can('distribuicao_editar')
                                    <x-action-link href="{{ route('distribuicoes.edit', $distribuicao) }}" class="text-green-600 hover:text-green-900">
                                        Editar
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
