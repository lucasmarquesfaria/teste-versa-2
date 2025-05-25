@props([
    'titulo',
    'descricao' => null,
    'acoes' => null,
    'filtros' => null,
    'alertas' => null
])

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-page-header :title="$titulo" :description="$descricao">
            {{ $acoes ?? '' }}
        </x-page-header>

        @if($filtros)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                {{ $filtros }}
            </div>
        </div>
        @endif

        @if($alertas)
        <div class="mb-6">
            {{ $alertas }}
        </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
