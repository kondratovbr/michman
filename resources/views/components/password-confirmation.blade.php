@props(['title', 'content', 'button'])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
    <x-modals.dialog wireModel="confirmingPassword">
        <x-slot name="header">
            {{ $title ?? __('passwords.confirm-title') }}
        </x-slot>

        <x-slot name="content">
            {{ $content ?? __('passwords.please-confirm') }}

            <x-field
                class="mt-4"
                x-data="{}"
                x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)"
            >
                <x-label>{{ __('forms.password.label') }}</x-label>
                <x-inputs.password
                    name="password"
                    id="confirmable_password"
                    class="max-w-xl"
                    x-ref="confirmable_password"
                    wire:model.defer="confirmablePassword"
                    wire:keydown.enter="confirmPassword"
                />
                <x-input-error for="confirmable_password" />
            </x-field>
        </x-slot>

        <x-slot name="actions">
            <x-buttons>
                <x-buttons.primary
                    wire:click="confirmPassword"
                    wire:loading.attr="disabled"
                >
                    {{ $button ?? __('buttons.confirm') }}
                </x-buttons.primary>
                <x-buttons.secondary
                    wire:click="stopConfirmingPassword"
                    wire:loading.attr="disabled"
                >
                    {{ __('buttons.cancel') }}
                </x-buttons.secondary>
            </x-buttons>
        </x-slot>
    </x-modals.dialog>
@endonce
