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
    loading="{{ ($status ?? $python->status) === Python::STATUS_INSTALLING }}"
>{{ __('servers.pythons.statuses.' . ($status ?? $python->status)) }}</x-badge>
