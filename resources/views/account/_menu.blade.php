{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}
{{--TODO: Maybe make it narrower on bigger screens (xl and 2xl). Looks too separated from the main content.--}}
{{--TODO: Figure out automatic active section highlight.--}}
{{--TODO: IMPORTANT! This menu is supposed to be a part of a big Livewire component (Maybe only Alpine?), so the page doesn't reload when these sections are changed.--}}
{{--TODO: Active link shouldn't hover?--}}
{{--TODO: Why are transitions a bit sluggish? Try with prod CSS (optimized)?--}}
{{--TODO: Make sure it looks OK in Russian on all screens as well. Text may not fit.--}}
{{--TODO: IMPORTANT! Don't forget to implement changes on other side menus if I do some here. Maybe DRY them out.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="profile">
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            {{ __('account.profile.button') }}
        </x-menu.item>

        <x-menu.item show="ssh">
            <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
            {{ __('account.ssh.button') }}
        </x-menu.item>

        <x-menu.item show="providers">
            <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
            {{ __('account.providers.button') }}
        </x-menu.item>

        <x-menu.item show="vcs">
            <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
            {{ __('account.vcs.button') }}
        </x-menu.item>

        @if(config('features.michman_api'))
            <x-menu.item show="api">
    {{--            TODO: Maybe put a Michman logo or some other icon here.--}}
                <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
                {{ __('account.api.button') }}
            </x-menu.item>
        @endif

    </ul>
</aside>
