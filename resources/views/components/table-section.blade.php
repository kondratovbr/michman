<div {{ $attributes }}>

    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        @isset($description)
            <x-slot name="description">{{ $description }}</x-slot>
        @endisset
        @isset($titleActions)
            <x-slot name="titleActions">{{ $titleActions }}</x-slot>
        @endisset
    </x-section-title>

{{--    TODO: CRITICAL! The "overflow-x-auto" here was a temporary crotch for tables that don't quite fit. Check that all tables look OK on all screens.--}}
    <x-box class="mt-5">
        @isset($empty)
            <x-box.content>
                {{ $empty }}
            </x-box.content>
        @else
            <table class="table-auto text-left w-full border-separate border-spacing-0">
                @isset($header)
                    <thead class="px-4 py-3 sm:px-6 bg-navy-200 border-b-2 border-gray-600">
                        {{ $header }}
                    </thead>
                @endisset
                <tbody>
                    {{ $body ?? $slot }}
                </tbody>
            </table>
        @endisset
        @isset($actions)
            <x-box.footer>{{ $actions }}</x-box.footer>
        @endisset
    </x-box>

    @isset($modal)
        {{ $modal }}
    @endisset

</div>
