@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'text-sm font-medium hover:underline']) }}>
    {{ $slot }}
</a>
