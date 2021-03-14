<x-layouts.app>
    <x-slot name="header">
        <h1 class="font-semibold text-xl leading-tight">
            {{ __('nav.account') }}
        </h1>
    </x-slot>

    <div class="md:grid md:grid-cols-12">
        <div class="md:col-span-3 px-5">
            @include('profile.menu')
        </div>
        <div class="md:col-span-9 max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')
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
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border/>
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>

</x-layouts.app>
