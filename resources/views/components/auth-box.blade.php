<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <x-auth-logo/>

    <x-box class="sm:max-w-md mt-6" shadow="shadow-lg-black">
        <x-slot name="content">
            {{ $slot }}
        </x-slot>
    </x-box>
</div>
