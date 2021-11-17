{{--TODO: CRITICAL! Adjust size for different screens.--}}
<x-smallbox class="w-40">
    <x-slot name="content">
        <div class="flex flex-col items-center justify-center">

            <span>{{ __("auth.oauth.providers.{$provider}.label") }}</span>

            <x-icon class="mt-1" size="16"><i class="fab fa-github fa-3x"></i></x-icon>

            <x-buttons.primary class="mt-3">Link</x-buttons.primary>

        </div>
    </x-slot>
</x-smallbox>
