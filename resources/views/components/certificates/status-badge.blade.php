@php
    use App\Models\Certificate;
@endphp

@props(['status', 'certificate'])

<x-badge
    colors="{{ match ($status ?? $certificate->status) {
        Certificate::STATUS_INSTALLED => 'success',
        default => null,
    } }}"
>{{ __('servers.ssl.statuses.' . ($status ?? $certificate->status)) }}</x-badge>
