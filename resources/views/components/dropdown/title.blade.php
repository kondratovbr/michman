{{--TODO: Is it super small and bleak, nigh unreadable? See the reference implementation (Jetstream) and just google dropdown styling in general.--}}

@props(['capitalize' => true, 'paddingLeft' => 'pl-10 md:pl-6'])

<div class="block {{ $paddingLeft }} pr-6 py-4 text-sm text-gray-400 select-none {{ $capitalize ? 'capitalize' : '' }}">
    {{ $slot }}
</div>
