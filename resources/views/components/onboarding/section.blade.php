<x-action-section>
    <x-slot name="title">
        ⛵️ Complete your account! (1/4)
    </x-slot>

    <div class="divide-y divide-navy-100 rounded-lg shadow relative z-10 mb-6">
        <ul class="divide-y divide-navy-100">

            <li class="flex items-center space-x-4 p-4">

                <x-onboarding.step-number :completed="true" />

                <div class="flex-1">
                    <h3 class="text-md font-medium">Add a Git provider</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        Add a Git provider to easily clone your repositories,
                        we support GitHub, Bitbucket &amp; GitLab.
                    </p>
                </div>
                <x-buttons.secondary :link="true" href="{{ route('account.show', 'vcs') }}">
                    Link Git provider
                </x-buttons.secondary>
            </li>

            <li class="flex items-center space-x-4 p-4">
                <x-onboarding.step-number>2</x-onboarding.step-number>
                <div class="flex-1">
                    <h3 class="text-md font-medium">Add a server provider</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        Michman manages your cloud servers for you,
                        so you need to connect a server provider.
                        Currently, DigitalOcean is the only supported provider.
                    </p>
                </div>
                <x-buttons.secondary :link="true" href="{{ route('account.show', 'providers') }}">
                    Link server provider
                </x-buttons.secondary>
            </li>

            <li class="flex items-center space-x-4 p-4">
                <x-onboarding.step-number>3</x-onboarding.step-number>
                <div class="flex-1">
                    <h3 class="text-md font-medium">Create your first server</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        This is the key step. Everything starts with a server.
                        You can do it on this page down below. 👇
                    </p>
                </div>
            </li>

            <li class="flex items-center space-x-4 p-4">
                <x-onboarding.step-number>4</x-onboarding.step-number>
                <div class="flex-1">
                    <h3 class="text-md font-medium">Add your first project</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        Add a project to Michman to deploy it on your server.
                    </p>
                </div>

                //

            </li>

            <li class="flex items-center space-x-4 p-4">
                <x-onboarding.step-number>5</x-onboarding.step-number>
                <div class="flex-1">
                    <h3 class="text-md font-medium">Configure your project</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        Point us to your Git repo, so we could deploy your project on your server.
                    </p>
                </div>

                //

            </li>

            <li class="flex items-center space-x-4 p-4">
                <x-onboarding.step-number>5</x-onboarding.step-number>
                <div class="flex-1">
                    <h3 class="text-md font-medium">Upgrade your plan</h3>
                    <p class="max-w-prose text-sm text-gray-400">
                        Upgrade your plan to lift restrictions and enjoy the most out of managing your services.
                    </p>
                </div>
                <x-buttons.secondary :link="true" href="/billing">
                    Upgrade plan
                </x-buttons.secondary>
            </li>
        </ul>
        <div
            class="border-primary-100 absolute inset-y-0 left-8 -ml-px border-l-2 border-dashed"
            style="z-index: -1;"
        ></div>
    </div>

</x-action-section>
