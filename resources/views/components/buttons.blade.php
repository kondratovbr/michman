{{--TODO: Smaller spacing in mobile?--}}
{{--TODO: IMPORTANT! Check other places I'm using this component - the buttons tend to be skewed vertically for some reason.--}}

<div {{ $attributes->merge([
    // "-mb-x" compensates for the bottom margins introduced onto the buttons by space-y-x-bottom.
    'class' => 'flex flex-wrap items-start space-x-3-right space-y-3-bottom -mb-3',
]) }}>{{ $slot }}</div>
