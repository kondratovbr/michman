<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <x-auth-logo/>

    @isset($title)
        <h1 class="mt-3 flex justify-center text-xl">
            {{ $title }}
        </h1>
    @endisset

    <x-box class="sm:max-w-md mt-4" shadow="shadow-lg">
        <x-slot name="content">
            {{ $slot }}
        </x-slot>
    </x-box>

    @isset($bottomMessage)
        <x-box class="sm:max-w-md mt-6" :secondary="true">
            <x-slot name="content">
                {{ $bottomMessage }}
            </x-slot>
        </x-box>
    @endisset
</div>
