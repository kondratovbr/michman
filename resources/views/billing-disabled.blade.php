<x-layouts.app-one-column>

    <x-slot name="header">
        <x-page-title>Billing Disabled</x-page-title>
    </x-slot>

    <div class="space-y-10 sm:space-y-0">
        <x-box class="relative">
            <x-box.content class="relative">

                <div class="block float-right ml-4 mb-4 rounded-full shadow-xl">
                    <img
                        class="h-20 w-20 md:h-32 md:w-32 rounded-full"
                        src="https://github.com/kondratovbr.png"
                        alt="Avatar photo of Bogdan Kondratov"
                    >
                </div>

                <div class="prose md:prose-xl max-w-prose text-gray-100">

                    <p class="font-bold text-2xl">Ahoy!</p>

                    <p>Thanks for your interest in subscribing to Michman!</p>

                    <p>
                        Right now Michman is still in Beta, and I'm not comfortable charging money for it,
                        so the billing is currently disabled.
                    </p>

                    <p>
                        You can keep using all features of Michman for free, no subscription is required.
                        I will notify you when the billing is turned on, and you'll get a free 30-day
                        grace period before a subscription will be required.
                    </p>

                    <p>In the mean time I would much appreciate your feedback or any suggestions you have for Michman.</p>

                </div>

                <x-buttons.primary
                    class="mt-5"
                    :link="true"
                    href="mailto:{{ trim(config('app.support_email')) }}"
                >Contact Support</x-buttons.primary>

            </x-box.content>
        </x-box>
    </div>

</x-layouts.app-one-column>
