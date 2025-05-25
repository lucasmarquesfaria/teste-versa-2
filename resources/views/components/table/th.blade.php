@props(['label'])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider']) }}>
    {{ $label }}
</th>
