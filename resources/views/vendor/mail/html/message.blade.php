@component('mail::layout')

    {{-- Header --}}
    @slot('header')
        @component('mail::header')
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')

            @component('mail::footer-section')
                @slot('left')
                    [Twitter](https://twitter.com/michman_dev)
                    [Reddit](https://www.reddit.com/r/michman/)
                    [GitHub](https://github.com/michman-dev)
                    [Terms of Service](https://michman.dev/terms-of-service)
                @endslot
                @slot('right')
                    {{ config('app.name') }} {{ date('Y') }}
                @endslot
            @endcomponent

        @endcomponent
    @endslot

@endcomponent
