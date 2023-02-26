{{--NOTE: There's no indentations here because the content will be rendered as Markdown.--}}
@component('mail::message')

{{-- Greeting --}}
@if(! empty($greeting))
# {{ $greeting }}
@else
@if (($level ?? null) === 'error')
# @lang('notifications.whoops')
@else
# @lang('notifications.hello')
@endif
@endif

{{-- Intro Lines --}}
@foreach($introLines ?? [] as $line)
{{ $line }}
@endforeach

{{-- Action Button --}}
@isset($actionText)
@php
$color = match ($level ?? null) {
    'success', 'error' => $level,
    default => 'primary',
};
@endphp
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach($outroLines ?? [] as $line)
{{ $line }}
@endforeach

{{-- Salutation --}}
@if(! empty($salutation))
{{ $salutation }}
@else
{{ config('app.name') }},<br>
@lang('notifications.over')
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang('notifications.cant-click') <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset

@endcomponent
