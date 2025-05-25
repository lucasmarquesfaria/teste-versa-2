<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Baixas') }}
            </h2>
            <div class="flex space-x-2">
                @can('baixa_criar')
                    <x-primary-button-link href="{{ route('baixas.create') }}">
                        <x-icons.plus class="h-4 w-4 mr-1" />
                        {{ __('Nova Baixa') }}
                    </x-primary-button-link>
                    <x-secondary-button-link href="{{ route('baixas.create-lote') }}">
                        <x-icons.list class="h-4 w-4 mr-1" />
                        {{ __('Baixa em Lote') }}
                    </x-secondary-button-link>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-layouts.main-page titulo="Baixas" descricao="Gerencie as baixas de formulários">
        <x-slot name="filtros">
            <x-page-filter-panel :route="route('baixas.index')">
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
                    <x-input-label for="situacao" :value="__('Situação')" />
                    <x-select-input id="situacao" name="situacao" class="mt-1 block w-full">
                        <option value="">Todas</option>
                        <option value="utilizada" @selected(request('situacao') == 'utilizada')>Utilizada</option>
                        <option value="cancelada" @selected(request('situacao') == 'cancelada')>Cancelada</option>
                        <option value="nao_utilizada" @selected(request('situacao') == 'nao_utilizada')>Não Utilizada</option>
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

        <x-data-table :data="$baixas">
            <x-slot name="header">
                <x-table.th label="Instituição" />
                <x-table.th label="Tipo" />
                <x-table.th label="Número" />
                <x-table.th label="Data Devolução" />
                <x-table.th label="Situação" />
                <x-table.th label="Ações" />
            </x-slot>

            <x-slot name="row">
                @foreach($baixas as $baixa)
                    <tr>
                        <x-table.td>{{ $baixa->distribuicao->instituicao->nome }}</x-table.td>
                        <x-table.td>
                            @if($baixa->distribuicao->tipo_certidao == 'obito')
                                <x-status-badge type="info">DO</x-status-badge>
                            @else
                                <x-status-badge type="info">DNV</x-status-badge>
                            @endif
                        </x-table.td>
                        <x-table.td>{{ $baixa->numero }}</x-table.td>
                        <x-table.td>@data($baixa->data_devolucao)</x-table.td>
                        <x-table.td>
                            @if($baixa->situacao == 'utilizada')
                                <x-status-badge type="success">Utilizada</x-status-badge>
                            @elseif($baixa->situacao == 'cancelada')
                                <x-status-badge type="danger">Cancelada</x-status-badge>
                            @else
                                <x-status-badge>Não Utilizada</x-status-badge>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <div class="flex space-x-2">
                                @can('baixa_visualizar')
                                    <x-action-link href="{{ route('baixas.show', $baixa) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Detalhes
                                    </x-action-link>
                                @endcan

                                @can('baixa_editar')
                                    <x-action-link href="{{ route('baixas.edit', $baixa) }}" class="text-green-600 hover:text-green-900">
                                        Editar
                                    </x-action-link>
                                @endcan

                                @can('baixa_excluir')
                                    <form action="{{ route('baixas.destroy', $baixa) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta baixa?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                @endcan
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-slot>
        </x-data-table>
    </x-layouts.main-page>
</x-app-layout>
