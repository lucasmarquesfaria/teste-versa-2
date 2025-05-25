@props(['status' => 'normal'])

@php
    $baseClasses = 'px-6 py-4 whitespace-nowrap text-sm';
    $statusClasses = [
        'normal' => 'text-gray-900',
        'muted' => 'text-gray-500',
        'success' => 'text-green-600',
        'danger' => 'text-red-600',
        'warning' => 'text-yellow-600',
        'primary' => 'text-indigo-600'
    ];
@endphp

<td {{ $attributes->merge(['class' => $baseClasses . ' ' . ($statusClasses[$status] ?? $statusClasses['normal'])]) }}>
    {{ $slot }}
</td>
