{{--TODO: IMPORTANT! Unfinished. Need to change from hover to group-hover, and expand the interactive area so there's no gaps. Gaps are annoying.
        Also, probably make it narrower and put closer to the center.
        Also, figure out smaller screens layout.
        Also, add icons. --}}
{{--TODO: Figure out automatic active section highlight.--}}
{{--TODO: IMPORTANT! This menu is supposed to be a part of a big Livewire component (Maybe only Alpine?), so the page doesn't reload when these sections are changed.--}}

<aside>
    <ul class="space-y-2 text-gray-200">

        <x-menu.item>Profile</x-menu.item>

        <x-menu.item
            class="bg-navy-300 text-gray-100"
        >SSH Keys</x-menu.item>
        <x-menu.item>Server Provider</x-menu.item>
        <x-menu.item>Source Control</x-menu.item>
        <x-menu.item>Michman API</x-menu.item>
    </ul>
</aside>
