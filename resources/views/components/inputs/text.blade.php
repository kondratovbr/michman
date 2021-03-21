@props(['showErrors' => true])

<div class="relative">

    <x-input {{ $attributes->merge([
        'class' => 'sibling px-3' . (isset($iconLeft) ? ' pl-11-sub-2' : ' pl-2') . (isset($iconRight) ? ' pr-11-sub-2' : ' pr-2'),
        'type' => 'text',
    ]) }}
        :showErrors="$showErrors"
    />

    @isset($iconLeft)
        <x-icon
            size="11"
            class="absolute top-0 left-0 text-xl text-gray-400 sibling-focus:text-gray-300 transition-text ease-in-out duration-quick"
        >{{ $iconLeft }}</x-icon>
    @endisset

    @isset($iconRight)
        <x-icon
            size="11"
            class="absolute top-0 right-0 text-xl text-gray-400 sibling-focus:text-gray-300 transition-text ease-in-out duration-quick"
        >{{ $iconRight }}</x-icon>
    @endisset

</div>
