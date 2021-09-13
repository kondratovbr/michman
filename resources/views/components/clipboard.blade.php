{{--TODO: Test this thoroughly with various devices/browsers/screens and move this to a separate component to make HTML smaller when there's a lot of these things.--}}
{{--TODO: Maybe make this thing react on click somehow. Just not much, this isn't even a button afrer all.--}}

@props(['value', 'tooltip' => true])

<div
    class="inline-block cursor-pointer"
    x-data="{
        tooltip: '{{ __('misc.copy-clipboard') }}',
        success: '{{ __('misc.copied') }}',
        copied: false,
        timeout: null,
    }"
    x-on:click.prevent="
        $clipboard('{{ $value ?? $slot }}');
        clearTimeout(timeout);
        copied = true;
        timeout = setTimeout(() => { copied = false }, 1000);
    "
    @if($tooltip ?? true)
        x-tooltip.keep-on-click="copied ? success : tooltip"
    @endif
>
    <span>{{ $slot }}</span>
    <x-icon class="ml-1"><i class="far fa-clipboard"></i></x-icon>
</div>
