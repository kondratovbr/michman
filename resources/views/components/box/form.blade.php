@isset($header)
    <x-box.header>
        {{ $header }}
    </x-box.header>
@endisset

<x-box.content>
    <x-dynamic-component
        :component="$formComponent"
        {{ $attributes }}
        :method="$method"
        :withFiles="$withFiles"
    >
        {{ $content ?? $slot }}
    </x-dynamic-component>
</x-box.content>

@isset($actions)
    <x-box.footer>
        {{ $actions }}
    </x-box.footer>
@endisset
