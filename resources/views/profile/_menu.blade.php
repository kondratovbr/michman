{{--TODO: Maybe make it narrower on bigger screens (xl and 2xl). Looks too separated from the main content.--}}
{{--TODO: Figure out automatic active section highlight.--}}
{{--TODO: IMPORTANT! This menu is supposed to be a part of a big Livewire component (Maybe only Alpine?), so the page doesn't reload when these sections are changed.--}}
{{--TODO: Active link shouldn't hover?--}}
{{--TODO: Why are transitions a bit sluggish? Try with prod CSS (optimized)?--}}

<aside>
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item>
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            {{ __('account.profile.button') }}
        </x-menu.item>

        <x-menu.item
{{--            TODO: Changed the way transitions work on the item itself. Now this approach doesn't work. Improve.--}}
            class="bg-navy-300 text-gray-100"
        >
            <x-slot name="icon"><i class="fa fa-terminal"></i></x-slot>
            {{ __('account.ssh.button') }}
        </x-menu.item>

        <x-menu.item>
            <x-slot name="icon"><i class="fa fa-server"></i></x-slot>
            {{ __('account.providers.button') }}
        </x-menu.item>

        <x-menu.item>
            <x-slot name="icon"><i class="fa fa-code-branch"></i></x-slot>
            {{ __('account.vcs.button') }}
        </x-menu.item>

        <x-menu.item>
{{--            TODO: Maybe put a Michman logo or some other icon here.--}}
            <x-slot name="icon"><i class="fa fa-ship"></i></x-slot>
            {{ __('account.api.button') }}
        </x-menu.item>

    </ul>
</aside>
