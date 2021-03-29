<li>
    <a
        class="group w-full h-full inline-flex flex-col items-stretch py-1 cursor-pointer select-none focus:outline-none"
    >
        <div
            class="capitalize flex items-center py-3 px-4 rounded-lg border border-gray-300 border-opacity-0 bg-navy-300 bg-opacity-0 group-hover:border-opacity-100 group-hover:text-gray-100 group-active:bg-opacity-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
        >
            <div class="transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
                @isset($icon)
                    <x-icon class="mr-2">{{ $icon }}</x-icon>
                @endisset
                <span>{{ $slot }}</span>
            </div>
        </div>
    </a>
</li>
