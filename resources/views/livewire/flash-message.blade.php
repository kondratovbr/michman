{{--TODO: CRITICAL! Unfinished. Adapt to mobile. Make screen-wide on tiny screens and almost screen-wide on small ones.--}}
{{--TODO: I'll have to assess the a11y of this element and this whole system and maybe rethink it.--}}

@php
use App\Events\Users\FlashMessageEvent;
@endphp

<div
    class="fixed top-20 right-4 w-96 z-50"
    x-data="{ show: @entangle('show'), timeout: null }"
    x-init="$watch('show', (value) => {
        if (! show) return;
        clearTimeout(timeout);
        timeout = setTimeout(() => { show = false }, 5000);
    })"
    x-show="show"
    x-transition
    x-cloak
    wire:key="flash-message"
>
    <div class="{{ implode(' ', [
        match ($style ?? null) {
            FlashMessageEvent::STYLE_INFO       => 'bg-navy-500',
            FlashMessageEvent::STYLE_SUCCESS    => 'bg-green-800 text-green-100',
            FlashMessageEvent::STYLE_WARNING    => 'bg-yellow-400 text-yellow-900',
            FlashMessageEvent::STYLE_DANGER     => 'bg-red-700 text-red-100',
            default                             => 'bg-gray-700 text-gray-100',
        },
        'shadow-lg rounded-lg py-3 px-3',
    ]) }}">
        <div class="flex items-stretch justify-between space-x-3">
            <div class="flex items-start">
                <x-icon size="8">
                    @switch($style ?? null)
                        @case(FlashMessageEvent::STYLE_INFO)
{{--                            TODO: CRITICAL! Put a different info icon here - the one from the paid version of FA: "far fa-info-circle"--}}
                            <i class="fas fa-info-circle fa-2x"></i>
                            @break
                        @case(FlashMessageEvent::STYLE_SUCCESS)
                            <i class="far fa-check-circle fa-2x"></i>
                            @break
                        @case(FlashMessageEvent::STYLE_WARNING)
{{--                            TODO: CRITICAL! Put a different triangle icon here - the one from the paid version of FA: "far fa-exclamation-triangle"--}}
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            @break
                        @case(FlashMessageEvent::STYLE_DANGER)
{{--                            TODO: CRITICAL! Put a different triangle icon here - the one from the paid version of FA: "far fa-exclamation-triangle"--}}
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            @break
                        @default
{{--                            TODO: CRITICAL! Put a different info icon here - the one from the paid version of FA: "far fa-info-circle"--}}
                            <i class="fas fa-info-circle fa-2x"></i>
                    @endswitch
                </x-icon>
            </div>

            <div
                class="shrink flex items-center font-medium text-sm"
            >{{ $message }}</div>

            <div class="flex items-start">
                <x-buttons.close
                    size="small"
                    x-on:click.prevent="show = false"
                />
            </div>
        </div>
    </div>
</div>
