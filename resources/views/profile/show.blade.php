<x-layouts.app-with-menu>

    <x-slot name="header">
        <x-page-title>
            {{ __('nav.account') }}
        </x-page-title>
    </x-slot>

    <x-slot name="menu">
        @include('profile._menu')
    </x-slot>

    <div class="space-y-10 sm:space-y-0">

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <livewire:profile.tfa-form/>
            <x-section-separator/>
        @endif

        <livewire:profile.change-email-form/>
        <x-section-separator/>

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <livewire:profile.change-password-form/>
            <x-section-separator/>
        @endif

        <livewire:profile.logout-sessions-form/>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-section-separator/>
            <livewire:profile.delete-account-form/>
        @endif

    </div>

</x-layouts.app-with-menu>
