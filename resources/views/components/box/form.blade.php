<x-form
    {{ $attributes }}
    :method="$method"
    :withFiles="$withFiles"
>
    @isset($header)
        <x-box.header>
            {{ $header }}
        </x-box.header>
    @endisset

    <x-box.content>
        {{ $content ?? $slot }}
    </x-box.content>

    @isset($actions)
        <x-box.footer>
            {{ $actions }}
        </x-box.footer>
    @endisset
</x-form>
