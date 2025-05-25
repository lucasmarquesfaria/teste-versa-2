@props(['data'])

<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-50">
            <tr>
                {{ $header }}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @if(isset($row))
                {{ $row }}
            @else
                <tr>
                    <td colspan="50" class="px-6 py-4 text-center text-gray-500">
                        {{ $empty ?? __('Nenhum registro encontrado') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if(method_exists($data, 'links'))
    <div class="mt-4">
        {{ $data->links() }}
    </div>
@endif
