<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('nav.account') }}
        </h2>
    </x-slot>

    <div class="container mx-auto md:grid md:grid-cols-4 md:gap-6">
        <div class="md:col-span-1 py-10 px-5">
            @include('profile.menu')
        </div>
        <div class="md:col-span-3 max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

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
