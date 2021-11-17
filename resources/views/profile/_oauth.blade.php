<x-multiaction-section>

    <x-slot name="title">OAuth</x-slot>

    <x-slot name="content">

        <livewire:profile.o-auth-link-form provider="github"/>
        <livewire:profile.o-auth-link-form provider="gitlab"/>
        <livewire:profile.o-auth-link-form provider="bitbucket"/>

    </x-slot>

</x-multiaction-section>
