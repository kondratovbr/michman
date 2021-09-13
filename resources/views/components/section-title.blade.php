<div {{ $attributes }}>
    <div class="px-4 sm:px-0">

        @isset($titleActions)
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-100">{{ $title }}</h3>
                <div>{{ $titleActions }}</div>
            </div>
        @else
            <h3 class="text-lg font-medium text-gray-100">{{ $title }}</h3>
        @endisset

        @isset($description)
            <p class="mt-1 text-sm text-gray-300 max-w-prose">{{ $description }}</p>
        @endisset

    </div>
</div>
