@props(['status' => 'normal'])

@php
    $baseClasses = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
    $statusClasses = [
        'normal' => '',
        'muted' => 'text-gray-900',
        'success' => 'text-gray-900',
        'danger' => 'text-gray-900', 
        'warning' => 'text-gray-900',
        'primary' => 'text-gray-900'
    ];
@endphp

<td {{ $attributes->merge(['class' => $baseClasses . ' ' . ($statusClasses[$status] ?? $statusClasses['normal'])]) }}>
    {{ $slot }}
</td>
