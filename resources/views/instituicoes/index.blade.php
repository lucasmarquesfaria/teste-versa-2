<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Instituições') }}
            </h2>
            @can('instituicao_criar')
                <x-primary-button-link href="{{ route('instituicoes.create') }}">
                    <x-icons.plus class="h-4 w-4 mr-1" />
                    {{ __('Nova Instituição') }}
                </x-primary-button-link>
            @endcan
        </div>
    </x-slot>

    <x-layouts.main-page titulo="Instituições" descricao="Gerencie as instituições cadastradas no sistema">
        <x-slot name="filtros">
            <x-page-filter-panel :route="route('instituicoes.index')">
                <div>
                    <x-input-label for="search" :value="__('Pesquisar')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')" placeholder="Nome ou endereço..." />
                </div>
            </x-page-filter-panel>
        </x-slot>
        
        <x-data-table :data="$instituicoes">
            <x-slot name="header">
                <x-table.th label="Nome" />
                <x-table.th label="Endereço" />
                <x-table.th label="Telefone" />
                <x-table.th label="Ações" />
            </x-slot>

            <x-slot name="row">
                @foreach($instituicoes as $instituicao)
                    <tr>
                        <x-table.td>{{ $instituicao->nome }}</x-table.td>
                        <x-table.td status="muted">{{ $instituicao->endereco ?: 'Não informado' }}</x-table.td>
                        <x-table.td status="muted">{{ $instituicao->telefone ?: 'Não informado' }}</x-table.td>
                        <x-table.td>
                            <div class="flex space-x-2">
                                @can('instituicao_visualizar')
                                    <x-action-link href="{{ route('instituicoes.show', $instituicao) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Detalhes
                                    </x-action-link>
                                @endcan
                                @can('instituicao_editar')
                                    <x-action-link href="{{ route('instituicoes.edit', $instituicao) }}" class="text-green-600 hover:text-green-900">
                                        Editar
                                    </x-action-link>
                                @endcan
                                @can('instituicao_excluir')
                                    <form action="{{ route('instituicoes.destroy', $instituicao) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta instituição?');" class="inline">
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
