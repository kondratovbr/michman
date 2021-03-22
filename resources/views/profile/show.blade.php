<x-layouts.app-with-menu>

    <x-slot name="header">
        <x-page-title>
            {{ __('nav.account') }}
        </x-page-title>
    </x-slot>

    <x-slot name="menu">
        @include('profile.menu')
    </x-slot>

    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="mt-0">
            @livewire('profile.update-profile-information-form')
        </div>
        <x-section-border/>
    @endif

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="mt-10 sm:mt-0">
            @livewire('profile.update-password-form')
        </div>
        <x-section-border/>
    @endif

    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="mt-10 sm:mt-0">
            @livewire('profile.two-factor-authentication-form')
        </div>
        <x-section-border/>
    @endif

    <div class="mt-10 sm:mt-0">
        <livewire:profile.logout-sessions-form/>
    </div>

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <x-section-border/>
        <div class="mt-10 sm:mt-0">
            @livewire('profile.delete-user-form')
        </div>
    @endif

</x-layouts.app-with-menu>
