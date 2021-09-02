@php
    use App\Models\Certificate;
@endphp

@props(['status', 'certificate'])

<x-badge
    colors="{{ match ($status ?? $certificate->status) {
        Certificate::STATUS_INSTALLED => 'success',
        Certificate::STATUS_INSTALLING => null,
        default => null,
    } }}"
>{{ __('servers.ssl.statuses.' . ($status ?? $certificate->status)) }}</x-badge>
