@props(['shadow' => 'shadow-md', 'secondary' => false])

<div {{ $attributes->class([
    $secondary
        ? 'border-2 border-gray-500 w-full-2 sm:w-full rounded-lg'
        : 'bg-navy-300 w-full sm:rounded-lg',
    $secondary ? null : $shadow,
]) }}>
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
