@props(['type' => 'default'])

@php
$colors = [
    'default' => 'bg-gray-100 text-gray-900',
    'success' => 'bg-green-100 text-gray-900',
    'danger' => 'bg-red-100 text-gray-900',
    'warning' => 'bg-yellow-100 text-gray-900',
    'primary' => 'bg-indigo-100 text-gray-900',
    'info' => 'bg-blue-100 text-gray-900',
];

$baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
$colorClasses = $colors[$type] ?? $colors['default'];
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$colorClasses}"]) }}>
    {{ $slot }}
</span>
