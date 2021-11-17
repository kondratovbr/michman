<div {{ $attributes->class([
    'bg-navy-300 rounded-lg',
]) }}>
    @isset($header)
        <x-box.header>
            {{ $header }}
        </x-box.header>
    @endisset

    @isset($content)
        <div class="px-2 py-3 sm:p-3">
            {{ $content }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <x-box.footer>
            {{ $footer }}
        </x-box.footer>
    @endisset
</div>
