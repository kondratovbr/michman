@props(['submit'])

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
        <x-box.form wire:submit.prevent="{{ $submit }}">

            @isset($header)
                <x-slot name="header">
                    {{ $header }}
                </x-slot>
            @endisset

            {{ $form }}

            <x-slot name="actions">
                {{ $actions }}
            </x-slot>

        </x-box.form>
    </x-box>

</div>
