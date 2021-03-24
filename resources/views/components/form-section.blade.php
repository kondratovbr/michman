@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>

    <x-box class="mt-5 md:mt-0 md:col-span-2">
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

    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

</div>
