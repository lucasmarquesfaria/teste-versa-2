@props(['type' => 'default'])

@php
$colors = [
    'default' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'danger' => 'bg-red-100 text-red-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'primary' => 'bg-indigo-100 text-indigo-800',
    'info' => 'bg-blue-100 text-blue-800',
];

$baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
$colorClasses = $colors[$type] ?? $colors['default'];
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$colorClasses}"]) }}>
    {{ $slot }}
</span>
