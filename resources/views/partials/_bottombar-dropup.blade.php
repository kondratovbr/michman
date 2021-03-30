<x-dropdown.link
    href="{{ route('profile.show') }}"
    :capitalize="false"
    textClasses="text-sm"
>
    <div class="flex items-center space-x-2">
        <x-avatar class="h-8 w-8" />
        <span>{{ user()->email }}</span>
    </div>
</x-dropdown.link>

<x-dropdown.separator/>

<x-form method="POST" action="{{ route('logout') }}" x-data="{}" x-ref="form">
    <x-dropdown.link
        x-on:click.prevent="$refs.form.submit()"
        role="button"
    >
        <x-slot name="icon"><i class="fa fa-sign-out-alt fa-flip-horizontal"></i></x-slot>
        {{ __('auth.logout') }}
    </x-dropdown.link>
</x-form>
