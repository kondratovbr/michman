@props(['shadow' => 'shadow-md'])

<div
    {{ $attributes->merge([
        'class' => 'bg-navy-300 sm:rounded-lg w-full' . ' ' . $shadow,
    ]) }}
>
    @isset($header)
        <x-box.header>
            {{ $header }}
        </x-box.header>
    @endisset

    @isset($content)
        <x-box.content>
            {{ $content }}
        </x-box.content>
    @endisset

    {{ $slot }}

    @isset($footer)
        <x-box.footer>
            {{ $footer }}
        </x-box.footer>
    @endisset
</div>
