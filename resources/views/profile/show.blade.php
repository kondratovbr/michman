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
            @livewire('profile.two-factor-authentication-form')
            <x-section-border/>
        @endif

        <livewire:profile.change-email-form/>
        <x-section-border/>

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            @livewire('profile.update-password-form')
            <x-section-border/>
        @endif

        <livewire:profile.logout-sessions-form/>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-section-border/>
            @livewire('profile.delete-user-form')
        @endif
    </div>

</x-layouts.app-with-menu>
