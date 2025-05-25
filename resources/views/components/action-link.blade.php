@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'text-sm font-medium text-gray-900 hover:underline']) }}>
    {{ $slot }}
</a>
