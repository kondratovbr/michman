{{-- TODO: IMPORTANT! Check how it looks with longer names and everything. --}}

{{--TODO: CRITICAL! I completely ignored the tablet and mobile layouts when updating the navbar multiple times. Don't forget them!--}}

<x-navbar.dropdown>
    <x-slot name="trigger">
        <div class="flex items-center space-x-2">
            <x-icon><i class="fa fa-hard-hat"></i></x-icon>
            <span>{{ __('nav.projects') }}</span>
        </div>
    </x-slot>
    <x-dropdown.menu>
        @foreach($user->projects as $project)
            <x-dropdown.link
                href="{{ route('projects.show', [$project, 'deployment']) }}"
                :capitalize="false"
            >
{{--                TODO: This icon looks rather dull. Any better options? Maybe some effects or slight animations? Should google it.--}}
{{--                TODO: CRITICAL! This icon should reflect project's status. Like, blink amber if something is wrong, for example. Also, maybe replace with a spinner when the project is being configured and make the link disabled. Maybe also make a spinner for when the project is being deployed.--}}
                <x-slot name="icon"><i class="fas fa-circle text-green-500 text-xs"></i></x-slot>
                {{ $project->domain }}
            </x-dropdown.link>
        @endforeach
    </x-dropdown.menu>
</x-navbar.dropdown>
