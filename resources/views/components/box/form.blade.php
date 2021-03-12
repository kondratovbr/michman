@props(['method' => null, 'withFiles' => false])

<x-form
    {{ $attributes }}
    :method="$method"
    :withFiles="$withFiles"
>
    <x-box.content>
        {{ $slot }}
    </x-box.content>
    @isset($actions)
        <x-box.footer>
            {{ $actions }}
        </x-box.footer>
    @endisset
</x-form>
