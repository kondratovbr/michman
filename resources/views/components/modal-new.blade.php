{{--TODO: IMPORTANT! Unfinished. Responsiveness, mobile. Test on touch - both tablet and mobile.--}}
{{-- Container for a modal with an opaque background --}}
<div
    x-data="{ show: false }"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-100 bg-navy-100 opacity-75"
    x-show="show"
>
    {{-- Container for the modal box itself --}}
    <div>
        {{ $slot }}
    </div>
</div>
