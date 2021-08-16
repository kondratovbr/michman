@props(['status', 'deployment'])

<x-badge colors="{{
    match ($status ?? $deployment->status) {
        Deployment::STATUS_PENDING => null,
        Deployment::STATUS_WORKING => 'info',
        Deployment::STATUS_FAILED => 'danger',
        Deployment::STATUS_COMPLETED => 'success',
    }
}}">{{ __('deployments.statuses.' . ($status ?? $deployment->status)) }}</x-badge>
