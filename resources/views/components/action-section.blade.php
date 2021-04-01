<div {{ $attributes->merge([
    'class' => 'lg:grid lg:grid-cols-3 lg:gap-6',
]) }}>

    <x-section-title class="lg:order-2 lg:col-span-1">
        <x-slot name="title">{{ $title }}</x-slot>
        @isset($description)
            <x-slot name="description">{{ $description }}</x-slot>
        @endisset
    </x-section-title>

    <x-box class="lg:order-1 lg:col-span-2 mt-5 lg:mt-0">
        <x-slot name="content">
            {{ $content ?? $slot }}
        </x-slot>
    </x-box>

</div>
