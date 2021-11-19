<div {{ $attributes }}>

    @isset($title)
        <x-section-title class="mb-5">
            <x-slot name="title">{{ $title }}</x-slot>
            @isset($description)
                <x-slot name="description">{{ $description }}</x-slot>
            @endisset
        </x-section-title>
    @endisset

    <div class="flex items-stretch space-x-4 mx-4 sm:mx-0">
        {{ $content ?? $slot }}
    </div>

</div>
