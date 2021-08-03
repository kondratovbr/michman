{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}
{{--TODO: CRITICAL! Make sure the irrelevant buttons aren't shown and menus cannot be accessed. Based on the project's type.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="deployment">
            <x-slot name="icon"><i class="fas fa-code-branch"></i></x-slot>
            {{ __('projects.deployment.button') }}
        </x-menu.item>

        <x-menu.item show="queue">
{{--            TODO: CRITICAL! Placeholder icon. The actual one - "fas fa-user-hard-hat".--}}
            <x-slot name="icon"><i class="fas fa-square"></i></x-slot>
            {{ __('projects.queue.button') }}
        </x-menu.item>

    </ul>
</aside>
