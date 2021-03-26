{{--TODO: Figure out highlighting a currently open page.--}}

@props(['active' => false])

<a {{ $attributes->merge([
    'class' => implode(' ', [
        'group h-full w-full flex flex-col items-stretch py-1 px-2 cursor-pointer select-none',
        'text-base font-medium',
        'transition-all duration-quick ease-in-out',
        'focus:outline-none',
        // $active
        //     ? 'border-indigo-400 bg-navy-500 focus:outline-none focus:bg-indigo-100 focus:border-indigo-700'
        //     : 'border-transparent hover:bg-navy-400 hover:border-gray-300 focus:outline-none focus:bg-gray-50 focus:border-gray-300',
    ])
]) }}>
    <div class="py-3 px-4 text-gray-200 rounded-md border border-gray-300 border-opacity-0 bg-navy-400 bg-opacity-0 group-hover:border-opacity-100 group-active:bg-opacity-100 group-hover:text-gray-100 group-focus:border-opacity-100 transition-border-background ease-in-out duration-quick">
        @isset($icon)
            <x-icon class="mr-2">{{ $icon }}</x-icon>
        @endisset
        {{ $slot }}
    </div>
</a>
