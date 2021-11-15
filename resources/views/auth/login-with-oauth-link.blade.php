<x-layouts.guest>
    <x-auth-box>

        <x-slot name="title">
            Link to an existing account
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-forms.vertical method="POST" action="{{ route('login') }}">

{{--            TODO: CRITICAL! CONTINUE. And don't forget controllers logic - I haven't implemented it yet.--}}

            //

        </x-forms.vertical>

    </x-auth-box>
</x-layouts.guest>
