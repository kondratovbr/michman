{{--TODO: Is it super small and bleak, nigh unreadable? See the reference implementation (Jetstream) and just google dropdown styling in general.--}}
<div {{ $attributes->merge(['class' => 'block px-4 py-2 text-xs text-gray-400']) }}>
    {{ $slot }}
</div>
