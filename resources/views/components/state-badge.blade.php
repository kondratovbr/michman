@props(['state'])

<x-badge
    colors="{{ $state::$colors ?? null }}"
    :loading="$state::$loading ?? false"
>{{ __($state::$langKey . '.' . ($state::class)) ?? getClassName($state) }}</x-badge>
