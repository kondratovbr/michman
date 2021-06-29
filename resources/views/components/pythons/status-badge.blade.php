@props(['status', 'python'])

@php
    use App\Models\Python;
@endphp

<x-badge
    colors="{{ match ($status ?? $python->status) {
        Python::STATUS_INSTALLED => 'success',
        Python::STATUS_INSTALLING => null,
        default => null,
    } }}"
    loading="{{ in_array($status ?? $python->status, [
        Python::STATUS_INSTALLING,
        Python::STATUS_UPDATING,
    ]) }}"
>{{ __('servers.pythons.statuses.' . ($status ?? $python->status)) }}</x-badge>
