@props(['for'])

@error($for)
    <p {{ $attributes->merge([
        'class' => 'text text-red-500',
    ]) }}>
        <x-icon><i class="fa fa-exclamation-circle"></i></x-icon>
        <span class="">{{ $message }}</span>
    </p>
@enderror
