<form
    {{ $attributes }}
>
    <x-box.content>
        {{ $slot }}
    </x-box.content>
    @isset($actions)
        <x-box.footer>
            {{ $actions }}
        </x-box.footer>
    @endisset
</form>
