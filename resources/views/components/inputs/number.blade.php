@props(['showErrors' => true, 'widthClass' => ''])

<div class="relative {{ $widthClass }}">

    <x-input
        {{ $attributes->class([
            'sibling px-3',
            isset($iconLeft) ? ' pl-11' : ' pl-2',
            isset($iconRight) ? ' pr-11' : ' pr-2'
        ])->merge([
            'type' => 'number'
        ]) }}

        :showErrors="$showErrors"
    />

    @isset($iconLeft)
        <x-icon
            size="11"
            {{-- "left-2xp" is here to compensate for 2xp border on input --}}
            class="absolute top-0 left-2px text-xl text-gray-400 sibling-focus:text-gray-300 transition-text ease-in-out duration-quick"
        >{{ $iconLeft }}</x-icon>
    @endisset

    @isset($iconRight)
        <x-icon
            size="11"
            {{-- "right-2xp" is here to compensate for 2xp border on input --}}
            class="absolute top-0 right-2px text-xl text-gray-400 sibling-focus:text-gray-300 transition-text ease-in-out duration-quick"
        >{{ $iconRight }}</x-icon>
    @endisset

</div>
