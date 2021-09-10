@php
use App\Models\Provider;
@endphp

@props(['status', 'provider'])

<x-badge colors="{{
    match ($status ?? $provider->status) {
        Provider::STATUS_ACTIVE => 'success',
        Provider::STATUS_READY => 'warning',
        Provider::STATUS_ERROR => 'danger',
        default => null,
    }
}}">{{ __('account.providers.statuses.' . ($status ?? $provider->status)) }}</x-badge>
