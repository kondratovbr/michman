{{--TODO: Is it super small and bleak, nigh unreadable? See the reference implementation (Jetstream) and just google dropdown styling in general.--}}

@props(['capitalize' => true])

<div class="block px-6 py-2 text-sm text-gray-400 select-none {{ $capitalize ? 'capitalize' : '' }}">
    {{ $slot }}
</div>
