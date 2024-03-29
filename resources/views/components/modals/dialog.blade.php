@props(['modalId'])

<x-modal-new
    id="{{ $modalId ?? md5($attributes->wire('model')) }}"
    {{ $attributes }}
>

{{--    TODO: IMPORTANT! Make sure it works on touch as intended. Check multiple browsers.--}}
    {{-- Sizing and centering container for the box --}}
    <div class="h-full w-full py-6 px-2 sm:px-0 md:w-4/5 md:px-0 lg:w-2/3 mx-auto">

        {{-- Box --}}
        <div class="w-full max-h-full min-h-0 bg-navy-300 rounded-lg flex flex-col"
            {{-- To prevent the modal closing on clicks inside the modal box itself --}}
            x-on:click.stop=""
        >

            @isset($header)
                <div class="flex items-center px-4 py-3 bg-navy-200 sm:px-6 rounded-t-lg">
                    {{ $header }}
                </div>
            @endisset

            <div class="min-h-0 px-4 py-5 sm:p-6 flex flex-col">
                {{ $content ?? $slot }}
            </div>

            {{ $slot }}

            @isset($actions)
                <div class="px-4 py-3 bg-navy-200 sm:px-6 rounded-b-lg">
                    {{ $actions }}
                </div>
            @endisset

        </div>

    </div>

</x-modal-new>
