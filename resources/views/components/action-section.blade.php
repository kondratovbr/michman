<div {{ $attributes }}>

    @isset($title)
        <x-section-title class="mb-5">
            <x-slot name="title">{{ $title }}</x-slot>
            @isset($description)
                <x-slot name="description">{{ $description }}</x-slot>
            @endisset
        </x-section-title>
    @endisset

    <x-box>

        {{ $slot }}

        @isset($content)
            <x-slot name="content">
                {{ $content }}
            </x-slot>
        @endisset

    </x-box>

</div>
