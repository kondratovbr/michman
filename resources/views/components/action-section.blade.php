<div class="md:grid md:grid-cols-3 md:gap-6" {{ $attributes }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <x-box class="mt-5 md:mt-0 md:col-span-2">
        <x-slot name="content">
            {{ $content }}
        </x-slot>
    </x-box>
</div>
