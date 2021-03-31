{{--TODO: Is it super small and bleak, nigh unreadable? See the reference implementation (Jetstream) and just google dropdown styling in general.--}}

@props(['capitalize' => true, 'paddingLeft' => 'pl-9 md:pl-6'])

<div class="block {{ $paddingLeft }} mb-0 md:mb-2 pr-6 py-4 rounded-t-md bg-navy-200 border-b border-gray-600 md:border-b-0 text-sm select-none {{ $capitalize ? 'capitalize' : '' }}">
    {{ $slot }}
</div>
