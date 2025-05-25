@props(['title', 'description' => null, 'actions' => null])

<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>
            @if($description)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $description }}
                </p>
            @endif
        </div>
        @if($actions)
            <div class="flex items-center gap-4">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
""