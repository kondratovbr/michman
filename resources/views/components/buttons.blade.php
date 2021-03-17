{{--TODO: Smaller spacing in mobile?--}}

<div {{ $attributes->merge([
    // "-mb-x" compensates for the bottom margins introduced onto the buttons by space-y-x-bottom.
    'class' => 'flex flex-wrap items-start space-x-3-right space-y-3-bottom -mb-3',
]) }}>{{ $slot }}</div>
