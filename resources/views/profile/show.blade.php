{{--    TODO: CRITICAL! Users can now have multiple OAuth accounts linked, so this is not correct. Reimplement.--}}
@if(user()->usesOauth())
    @include('profile._oauth')
    <x-section-separator/>
@endif

@if(user()->canAny(['enableTfa', 'disableTfa'], user()) && Laravel\Fortify\Features::canManageTwoFactorAuthentication())
    <livewire:profile.tfa-form/>
    <x-section-separator/>
@endif

@can('changeEmail', user())
    <livewire:profile.change-email-form/>
    <x-section-separator/>
@endcan

@if(user()->can('changePassword', user()) && Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
    <livewire:profile.change-password-form/>
    <x-section-separator/>
@endif

@if(user()->can('logoutOtherSessions', user()))
    <livewire:profile.logout-sessions-form/>
    @if(Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <x-section-separator/>
    @endif
@endif

@if(user()->can('deleteAccount', user()) && Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
    <livewire:profile.delete-account-form/>
@endif
