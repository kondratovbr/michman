@props(['wrap' => false])

<div {{ $attributes->class([
    'relative group',
    'bg-code-bg rounded-md',
    'px-3-em py-2-em',
    'text-code-text font-mono',
]) }}
    x-data="{
        tooltip: '{{ __('misc.copy-clipboard') }}',
        success: '{{ __('misc.copied') }}',
        copied: false,
        timeout: null,
    }"
>

    <pre
        class="{{ $wrap ? 'whitespace-pre-wrap break-all' : 'whitespace-pre overflow-x-scroll' }}"
    ><code x-ref="content">{{ $slot }}</code></pre>

    <x-buttons.secondary
        class="absolute top-2 right-2 bg-gray-600 text-gray-300 opacity-0 group-hover:opacity-100"
        size="small"
        x-on:click.prevent="
            $clipboard($refs.content.textContent);
            clearTimeout(timeout);
            copied = true;
            timeout = setTimeout(() => { copied = false }, 1000);
        "
        x-tooltip.keep-on-click="copied ? success : tooltip"
    >
        <x-icon class="-ml-1 mr-1"><i class="far fa-clipboard"></i></x-icon>
        {{ __('misc.copy') }}
    </x-buttons.secondary>

</div>
