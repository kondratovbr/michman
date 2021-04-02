<x-sub-page name="profile">

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

</x-sub-page>
