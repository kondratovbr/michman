
{{-- NOTE: This button as well as the menu iself is currently not in use. --}}

<button
    class="group inline-flex lg:hidden items-stretch p-2 cursor-pointer select-none focus:outline-none"
    x-on:click="open = !open"
>
    <div class="p-2 flex items-center justify-center rounded-md border border-gray-200 border-opacity-0 group-hover:border-opacity-100 group-hover:text-gray-100 bg-navy-400 bg-opacity-0 group-active:bg-opacity-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick">
        <div class="transform group-hover:scale-110 group-focus:scale-110 transition-transform ease-in-out duration-quick">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                    x-bind:class="{ 'hidden': open, 'inline-flex': !open }"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                    x-bind:class="{ 'hidden': !open, 'inline-flex': open }"
                    x-cloak
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </div>
    </div>
</button>
