<div {{ $attributes->merge([
    'class' => 'lg:grid lg:grid-cols-3 lg:gap-6 overflow-visible',
]) }}>

    <x-section-title class="lg:order-2 lg:col-span-1">
        <x-slot name="title">{{ $title }}</x-slot>
        @isset($description)
            <x-slot name="description">{{ $description }}</x-slot>
        @endisset
    </x-section-title>

{{--    TODO: CRITICAL! The "overflow-x-auto" is a temporary crotch for tables that don't quite fit. Should do something about them.--}}
    <x-box class="lg:order-1 lg:col-span-2 mt-5 lg:mt-0 overflow-x-auto">
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
    </x-box>

</div>
