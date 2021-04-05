{{--TODO: Figure out highlighting a currently open page.--}}

@props([
    'capitalize' => true,
    'textClasses' => '',
    'external' => false,
    'action' => null,
    'subPage' => '',
])

<a {{ $attributes->merge([
    'class' => 'group h-full flex flex-col items-stretch mx-4 md:mx-0 px-0 md:px-2 py-2 md:py-1 cursor-pointer select-none focus:outline-none border-b border-gray-600 md:border-b-0 last:border-b-0',
]) }}
    @if($external)
        target="_blank"
    @endisset

    @switch($action)
        @case('back')
            x-on:click="open = true; sub = ''"
            @break
        @case('close')
            x-on:click="open = false; sub = ''"
            @break
        @case('sub-page')
            wire:click="$emit('showSubPage', '{{ $subPage }}')"
            x-on:click="open = false; sub = ''"
            @break
        @case(null)
            @break
        @default
            x-on:click="open = false; sub = '{{ $action }}'"
    @endswitch

    @if($action !== null)
        role="button"
    @endif
>
    <div
        class="py-3 px-4 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick"
    >
        <div class="flex items-center justify-start text-gray-200 {{ $capitalize ? 'capitalize' : '' }} {{ $textClasses }} transform group-hover:scale-105 transition-transform ease-in-out duration-quick">
            <div>
                @isset($icon)
                    <x-icon class="mr-2">{{ $icon }}</x-icon>
                @endisset
                {{ $slot }}
            </div>
            @isset($iconRight)
                <x-icon class="ml-2">{{ $iconRight }}</x-icon>
            @endisset
        </div>
    </div>
</a>
