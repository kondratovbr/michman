@props(['submit'])

<div {{ $attributes->merge([
    //
]) }}>

    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        @isset($description)
            <x-slot name="description">{{ $description }}</x-slot>
        @endisset
    </x-section-title>

    <x-box class="mt-5">
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
