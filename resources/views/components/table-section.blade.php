<div {{ $attributes->merge([
    'class' => 'md:grid md:grid-cols-3 md:gap-6',
]) }}>
    <x-box class="mt-5 md:mt-0 md:col-span-2">
        <x-slot name="header">
            {{ $header }}
        </x-slot>
        <x-slot name="content">
            {{ $slot }}
        </x-slot>
    </x-box>
</div>
