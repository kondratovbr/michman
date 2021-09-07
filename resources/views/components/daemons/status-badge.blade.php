@php
    use App\Models\Daemon;
@endphp

@props(['status', 'daemon'])

<x-badge
    colors="{{ match ($status ?? $daemon->status) {
        Daemon::STATUS_ACTIVE => 'success',
        Daemon::STATUS_FAILED => 'danger',
        default => null,
    } }}"
>{{ __('servers.daemons.statuses.' . ($status ?? $daemon->status)) }}</x-badge>
