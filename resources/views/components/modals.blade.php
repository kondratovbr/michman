{{--TODO: IMPORTANT! Unfinished. Responsiveness, mobile. Test on touch - both tablet and mobile.--}}
{{-- Container For Multiple Modals --}}
<div
    x-data="{ show: false }"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-100 bg-navy-100 opacity-75"
    x-show="show"
>

    {{-- Opaque Full Page Background  --}}
    <div></div>

    {{-- Container With Multiple Modal Blocks --}}
    <div>
        {{ $slot }}
    </div>

</div>
