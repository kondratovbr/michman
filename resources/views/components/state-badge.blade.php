@props(['state'])

<x-badge
    colors="{{ $state::$colors ?? null }}"
>{{ __($state::$langKey . '.' . ($state::class)) ?? getClassName($state) }}</x-badge>
