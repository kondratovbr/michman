{{--TODO: CRITICAL! Values/links are placeholders. Don't forget to put the actual ones.--}}
{{--TODO: CRITICAL! Make sure the irrelevant buttons aren't shown and menus cannot be accessed. Based on the project's type.--}}

<aside x-data="{ current: '{{ $this->show }}' }">
    <ul class="flex flex-col items-stretch text-gray-200">

        <x-menu.item show="repo">
            <x-slot name="icon"><i class="fas fa-code-branch"></i></x-slot>
            {{ __('projects.repo.button') }}
        </x-menu.item>

    </ul>
</aside>
