{{--Container for a set of x-radio-card components.--}}
{{--TODO: Refactor my other radio-cards sets to use this wrapper.--}}
<div {{ $attributes->class([
    // Negative bottom margin compensates for the bottom margin on the elements.
    // -mb-6
    'flex flex-wrap space-x-6-right space-y-6-bottom',
]) }}>{{ $slot }}</div>
