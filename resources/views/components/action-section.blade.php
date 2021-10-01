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
        <x-slot name="content">
            {{ $content ?? $slot }}
        </x-slot>
    </x-box>

</div>
