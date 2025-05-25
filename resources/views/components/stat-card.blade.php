@props(['for', 'icon' => null])

<div {{ $attributes->merge(['class' => 'relative rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800']) }}>
    @if($icon)
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400">
            <i class="{{ $icon }} text-lg"></i>
        </div>
    @endif
    <div class="mt-4">
        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ $for }}</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
            {{ $slot }}
        </dd>
    </div>
</div>
