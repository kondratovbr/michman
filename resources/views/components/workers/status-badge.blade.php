@php
    use App\Models\Worker;
@endphp

@props(['status', 'worker'])

<x-badge
    colors="{{ match ($status ?? $worker->status) {
        Worker::STATUS_ACTIVE => 'success',
        Worker::STATUS_FAILED => 'danger',
        default => null,
    } }}"
>{{ __('projects.queue.statuses.' . ($status ?? $worker->status)) }}</x-badge>
