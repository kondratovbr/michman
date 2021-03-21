@props(['for'])

@error($for)
    <p {{ $attributes->merge([
        'class' => 'mt-1 text text-red-500',
    ]) }}>
        <x-icon><i class="fa fa-exclamation-circle"></i></x-icon>
        <span class="ml-1">{{ $message }}</span>
    </p>
@enderror
