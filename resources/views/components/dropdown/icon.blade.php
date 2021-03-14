<div {{ $attributes->merge(['class' => 'relative h-4 w-4 inline-flex items-center justify-center']) }}>
    <x-icon class="absolute top-0 left-0">
{{--        opacity-100 group-hover:opacity-0 transition-opacity ease-in-out duration-500--}}
        <i class="fa fa-chevron-down"
{{--            class="fa"--}}
{{--            x-bind:class="{'fa-chevron-down': !open, 'fa-times': open}"--}}
        ></i>
    </x-icon>
{{--    <x-icon class="absolute top-0 left-0 opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-500">--}}
{{--        <i class="fa fa-arrow-down"></i>--}}
{{--    </x-icon>--}}
</div>
