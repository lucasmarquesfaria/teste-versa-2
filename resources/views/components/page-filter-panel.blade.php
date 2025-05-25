@props([
    'route', 
    'method' => 'GET'
])

<form action="{{ $route }}" method="{{ $method }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    {{ $slot }}

    <div class="md:col-span-2 lg:col-span-4 flex items-end justify-end space-x-2">
        <x-primary-button type="submit">
            <x-icons.filter class="h-4 w-4 mr-1" />
            {{ __('Filtrar') }}
        </x-primary-button>

        <x-secondary-button-link :href="$route">
            <x-icons.x class="h-4 w-4 mr-1" />
            {{ __('Limpar') }}
        </x-secondary-button-link>
    </div>
</form>
