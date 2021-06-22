{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="projects">
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            Projects
        </x-menu.item>

        <x-menu.item show="pythons">
            <x-slot name="icon"><i class="far fa-user"></i></x-slot>
            Projects
        </x-menu.item>

    </ul>
</aside>
