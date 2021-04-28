@props(['status', 'provider'])

<x-badge colors="{{
    match ($status ?? $provider->status) {
        'active' => 'success',
        'ready' => 'warning',
        'error' => 'danger',
        'pending' => null,
    }
}}">{{ __('account.providers.statuses.' . ($status ?? $provider->status)) }}</x-badge>
