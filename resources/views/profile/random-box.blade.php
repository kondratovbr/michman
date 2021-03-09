<div class="md:grid md:grid-cols-3 md:gap-6">

    <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium text-gray-100">Random title</h3>

            <p class="mt-1 text-sm text-gray-300">
                Random description
            </p>
        </div>
    </div>

    <x-box class="mt-5 md:mt-0 md:col-span-2">
        <x-slot name="header">
            Box header
        </x-slot>

        <x-slot name="content">
            Box content
        </x-slot>

        <x-slot name="footer">
            Box footer
        </x-slot>
    </x-box>

</div>
