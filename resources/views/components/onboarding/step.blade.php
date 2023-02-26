@props(['step', 'completed' => false])

<li class="flex items-center space-x-4 p-4">

    <div
        class="flex items-center justify-center h-8 w-8 rounded-full border-2 border-navy-100 text-center text-md font-medium {{ $completed ? 'bg-green-500 text-gray-200' : 'bg-gray-200 text-gray-900' }}"
    > {{ $completed ? '✓' : $step }} </div>

    <div class="flex-1">

        <h3 class="text-md font-medium">{{ $title }}</h3>

        @isset($subtitle)
            <p class="max-w-prose text-sm text-gray-400">
                {{ $subtitle }}
            </p>
        @endisset
    </div>

    @unless($completed)
        {{ $slot }}
    @endunless

</li>
