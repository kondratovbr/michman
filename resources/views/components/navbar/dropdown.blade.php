{{--TODO: IMPORTANT! Does it work on touch? Is it supposed to?--}}
{{--TODO: Needs hover, focus. Also, a11y concerns for dropdowns.--}}
{{--TODO: Needs different styling for links for when user is on that page. --}}

<div {{ $attributes->merge([
    'class' => 'relative',
]) }}
    x-data="{ open: false }"
    {{-- TODO: IMPORTANT! Does this work on touch? --}}
    x-on:click.away="open = false"
    x-on:close.stop="open = false"
>

    {{-- Dropdown button --}}
{{--If a <button> is used here - in Safari the child div (inside the button) doesn't stretch vertically by any means. A bug in safari.--}}
    <a
        class="group py-2 h-full w-full flex items-stretch cursor-pointer focus:outline-none"
        {{-- TODO: IMPORTANT! Does this work on touch? --}}
        x-on:click.prevent="open = !open"
        role="button"
    >
        <div
            class="px-5 rounded-md flex items-center self-stretch select-none border border-gray-300 border-opacity-0 group-hover:border-opacity-100 bg-navy-400 group-active:bg-opacity-100 transition-border-background ease-in-out duration-quick"
            x-bind:class="{'bg-opacity-0': !open, 'bg-opacity-100': open}"
        >
            <div class="flex items-center transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
                {{ $trigger }}
                @if($chevron)
                    <x-dropdown.icon class="ml-2" />
                @endif
            </div>
        </div>
    </a>

    {{ $slot }}

</div>
